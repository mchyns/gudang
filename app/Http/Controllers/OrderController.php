<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * Admin sees pending/all orders.
     */
    public function index()
    {
        $role = Auth::user()->role;
        $ordersQuery = Order::with(['user', 'orderItems.product']);
        $activeOrderType = request()->input('order_type');

        // Keep admin list focused so dapur and supplier orders are not mixed by default.
        if (!$activeOrderType && $role === 'admin') {
            $activeOrderType = 'dapur_sale';
        }

        if (in_array($activeOrderType, ['dapur_sale', 'supplier_purchase'], true)) {
            $ordersQuery->where('order_type', $activeOrderType);
        }

        if ($role === 'superadmin') {
            $orders = $ordersQuery->latest()->get();
        } else {
            $orders = $ordersQuery
                ->orderByRaw("FIELD(status, 'pending', 'processed', 'completed', 'cancelled')")
                ->latest()
                ->get();
        }

        return view('orders.index', [
            'orders' => $orders,
            'activeOrderType' => $activeOrderType,
        ]);
    }

    /**
     * Display a listing for Dapur (My Orders).
     */
    public function dapurIndex()
    {
        $orders = Order::where('user_id', Auth::id())
                    ->where('order_type', 'dapur_sale')
                    ->with(['orderItems.product'])
                    ->latest()
                    ->get();

        return view('orders.dapur_index', compact('orders'));
    }

    public function dapurSalesNote(Order $order)
    {
        if (Auth::user()->role !== 'dapur') {
            abort(403);
        }

        if ((int) $order->user_id !== (int) Auth::id() || $order->order_type !== 'dapur_sale') {
            abort(403);
        }

        if ($order->status !== 'completed') {
            return back()->with('error', 'Nota penjualan gudang tersedia setelah order selesai.');
        }

        $order->load(['user', 'orderItems.product.supplier']);

        return view('orders.dapur_sales_note', [
            'order' => $order,
            'sellerName' => 'Gudang',
        ]);
    }

    public function supplierNotesIndex()
    {
        if (Auth::user()->role !== 'supplier') {
            abort(403);
        }

        $orders = Order::with(['user', 'orderItems.product'])
            ->where('order_type', 'supplier_purchase')
            ->whereHas('orderItems.product', function ($query) {
                $query->where('supplier_id', Auth::id());
            })
            ->latest()
            ->paginate(15);

        return view('orders.supplier_notes_index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource (Kitchen Checkout).
     */
    public function create(Request $request)
    {
        $products = Product::with('category')
            ->where('status', 'active')
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->where(function ($query) {
                $query->where('warehouse_stock', '>', 0)
                    ->orWhereHas('orderItems.order', function ($orderQuery) {
                        $orderQuery->where('order_type', 'supplier_purchase')
                            ->where('status', 'completed');
                    });
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->integer('category_id'));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = trim((string) $request->q);
                $query->where(function ($inner) use ($keyword) {
                    $inner->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('description', 'like', '%' . $keyword . '%');
                });
            })
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('orders.create', compact('products', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     * Dapur creates an order (Checkout).
     */
    public function store(Request $request)
    {
        // Request expects:
        // items: [ { product_id: 1, quantity: 5 }, ... ]
        // OR simpler forms for now: single product buy or multiple.
        // Let's assume a JSON payload for flexibility or a form array.

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'note' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Calculate total and Create Order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_type' => 'dapur_sale',
                'total_price' => 0, // Will update below
                'status' => 'pending',
                'shipping_status' => 'pending',
                'note' => $request->note ?? '-'
            ]);

            $totalPrice = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->status !== 'active' || !$product->price) {
                    throw new \Exception("Produk {$product->name} belum siap dijual oleh gudang.");
                }
                
                // Check warehouse stock for kitchen sales.
                if ($product->warehouse_stock < $item['quantity']) {
                    throw new \Exception("Stok gudang {$product->name} tidak mencukupi (Sisa: {$product->warehouse_stock})");
                }

                $product->decrement('warehouse_stock', $item['quantity']);

                $price = $product->price ?? $product->supplier_price; // Fallback if admin hasn't set price yet (should not happen ideally)
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $price
                ]);

                $totalPrice += ($price * $item['quantity']);
            }

            $order->update(['total_price' => $totalPrice]);

            ActivityLogger::log(
                'order.created',
                'Dapur membuat order #' . $order->id,
                $order,
                ['total_items' => count($validated['items']), 'total_price' => $totalPrice]
            );

            DB::commit();

            return redirect()->route('dapur.orders.my_orders')->with('success', 'Order telah dibuat! Mohon tunggu konfirmasi admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat order: ' . $e->getMessage());
        }
    }

    public function createSupplierPurchase(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            abort(403);
        }

        $sourceOrder = null;
        $suggestedQuantities = [];

        if ($request->filled('source_order_id')) {
            $sourceOrder = Order::with(['orderItems.product.category', 'user'])
                ->where('order_type', 'dapur_sale')
                ->findOrFail($request->integer('source_order_id'));

            $suggestedQuantities = $sourceOrder->orderItems
                ->groupBy('product_id')
                ->map(fn($items) => (int) $items->sum('quantity'))
                ->toArray();
        }

        $suppliers = \App\Models\User::query()
            ->where('role', 'supplier')
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        $products = Product::with(['supplier', 'category'])
            ->whereNotNull('supplier_id')
            ->where('status', 'active')
            ->when($request->filled('supplier_id'), function ($query) use ($request) {
                $query->where('supplier_id', $request->integer('supplier_id'));
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->integer('category_id'));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = trim((string) $request->q);
                $query->where(function ($inner) use ($keyword) {
                    $inner->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('description', 'like', '%' . $keyword . '%');
                });
            })
            ->orderBy('name')
            ->get();

        if (!empty($suggestedQuantities)) {
            $products = $products->sortByDesc(function ($product) use ($suggestedQuantities) {
                return $suggestedQuantities[$product->id] ?? 0;
            })->values();
        }

        return view('orders.supplier_purchase_create', compact('products', 'suppliers', 'categories', 'sourceOrder', 'suggestedQuantities'));
    }

    public function storeSupplierPurchase(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:1000',
            'source_order_id' => 'nullable|exists:orders,id',
        ]);

        try {
            DB::beginTransaction();

            $sourceOrder = null;
            if (!empty($validated['source_order_id'])) {
                $sourceOrder = Order::where('order_type', 'dapur_sale')->findOrFail((int) $validated['source_order_id']);
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_type' => 'supplier_purchase',
                'source_dapur_order_id' => $sourceOrder?->id,
                'total_price' => 0,
                'status' => 'pending',
                'shipping_status' => 'pending',
                'note' => $request->note ?: null,
            ]);

            $totalPrice = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::with('supplier')->findOrFail($item['product_id']);

                if (!$product->supplier_id) {
                    throw new \Exception("Produk {$product->name} tidak terhubung ke supplier.");
                }

                $unitPrice = (float) ($product->supplier_price ?? 0);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $unitPrice,
                ]);

                $totalPrice += ($unitPrice * $item['quantity']);
            }

            $order->update(['total_price' => $totalPrice]);

            if ($sourceOrder && $sourceOrder->status === 'pending') {
                $sourceOrder->update([
                    'status' => 'processed',
                    'shipping_status' => 'prepared',
                ]);
            }

            ActivityLogger::log(
                'order.supplier_purchase_created',
                'Gudang membuat pembelian supplier #' . $order->id,
                $order,
                [
                    'total_items' => count($validated['items']),
                    'total_price' => $totalPrice,
                    'source_dapur_order_id' => $sourceOrder?->id,
                ]
            );

            DB::commit();

            return redirect()->route('admin.orders.index', ['order_type' => 'supplier_purchase'])
                ->with('success', 'Permintaan pembelian supplier berhasil dibuat. Menunggu ACC dari supplier.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat pembelian supplier: ' . $e->getMessage());
        }
    }

    public function approveSupplierPurchase(Order $order)
    {
        $supplier = Auth::user();

        if ($supplier->role !== 'supplier') {
            abort(403);
        }

        if ($order->order_type !== 'supplier_purchase') {
            abort(404);
        }

        $order->load(['orderItems.product']);

        $pendingItems = $order->orderItems->filter(function ($item) use ($supplier) {
            return $item->product
                && (int) $item->product->supplier_id === (int) $supplier->id
                && $item->supplier_approved_at === null;
        });

        if ($pendingItems->isEmpty()) {
            return back()->with('error', 'Tidak ada item milik Anda yang menunggu ACC pada order ini.');
        }

        DB::transaction(function () use ($pendingItems, $supplier, $order) {
            foreach ($pendingItems as $item) {
                $item->update([
                    'supplier_approved_at' => now(),
                    'supplier_approved_by' => $supplier->id,
                ]);

            }

            $remainingPending = $order->orderItems()->whereNull('supplier_approved_at')->exists();

            $order->update([
                'status' => $remainingPending ? 'pending' : 'processed',
                'shipping_status' => $remainingPending ? 'pending' : 'shipped',
            ]);
        });

        ActivityLogger::log(
            'order.supplier_purchase_approved',
            'Supplier menyetujui item pembelian order #' . $order->id,
            $order,
            ['supplier_id' => $supplier->id, 'approved_items' => $pendingItems->count()]
        );

        return back()->with('success', 'Item pembelian Anda berhasil di-ACC dan dikirim ke gudang.');
    }

    public function receiveSupplierPurchase(Order $order)
    {
        if (!in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            abort(403);
        }

        if ($order->order_type !== 'supplier_purchase') {
            abort(404);
        }

        if ($order->status !== 'processed') {
            return back()->with('error', 'Order belum siap diterima. Tunggu semua item supplier di-ACC.');
        }

        DB::transaction(function () use ($order) {
            $order->load(['orderItems.product']);

            foreach ($order->orderItems as $item) {
                if (!$item->product || $item->supplier_approved_at === null) {
                    continue;
                }

                $item->product->increment('warehouse_stock', $item->quantity);

                if ((int) $item->product->warehouse_initial_stock === 0) {
                    $item->product->update([
                        'warehouse_initial_stock' => (int) $item->product->warehouse_stock,
                    ]);
                }
            }

            $order->update([
                'status' => 'completed',
                'shipping_status' => 'delivered',
            ]);
        });

        ActivityLogger::log(
            'order.supplier_purchase_received',
            'Gudang menerima barang supplier untuk order #' . $order->id,
            $order
        );

        return back()->with('success', 'Barang sudah diterima di gudang dan stok gudang diperbarui.');
    }

    /**
     * Admin updates status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processed,completed,cancelled'
        ]);

        // If cancelling, restore or rollback stock based on order type.
        if ($validated['status'] === 'cancelled' && $order->status !== 'cancelled') {
            foreach ($order->orderItems as $item) {
                if (!$item->product) {
                    continue;
                }

                if ($order->order_type === 'dapur_sale') {
                    $item->product->increment('warehouse_stock', $item->quantity);
                }

                if ($order->order_type === 'supplier_purchase' && $order->status === 'completed') {
                    $nextStock = max((int) $item->product->warehouse_stock - (int) $item->quantity, 0);
                    $item->product->update(['warehouse_stock' => $nextStock]);
                }
            }
        }

        $order->update(['status' => $validated['status']]);

        ActivityLogger::log(
            'order.status_updated',
            'Status order #' . $order->id . ' diubah menjadi ' . $validated['status'],
            $order,
            ['status' => $validated['status']]
        );

        return redirect()->route('admin.orders.index', ['order_type' => $order->order_type])
            ->with('success', "Order #{$order->id} status updated to " . ucfirst($validated['status']));
    }

    public function editOperational(Order $order)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        if ($order->order_type !== 'dapur_sale') {
            abort(404);
        }

        return view('orders.operational', compact('order'));
    }

    public function updateOperational(Request $request, Order $order)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->merge([
            'operational_bensin' => $this->normalizeNominalInput($request->input('operational_bensin')),
            'operational_kuli' => $this->normalizeNominalInput($request->input('operational_kuli')),
            'operational_makan_minum' => $this->normalizeNominalInput($request->input('operational_makan_minum')),
            'operational_listrik' => $this->normalizeNominalInput($request->input('operational_listrik')),
            'operational_wifi' => $this->normalizeNominalInput($request->input('operational_wifi')),
        ]);

        if ($order->order_type !== 'dapur_sale') {
            abort(404);
        }

        $validated = $request->validate([
            'operational_bensin' => 'nullable|numeric|min:0',
            'operational_kuli' => 'nullable|numeric|min:0',
            'operational_makan_minum' => 'nullable|numeric|min:0',
            'operational_listrik' => 'nullable|numeric|min:0',
            'operational_wifi' => 'nullable|numeric|min:0',
            'admin_note' => 'nullable|string|max:1000',
            'drop_date' => 'nullable|date',
            'shipping_status' => 'required|in:pending,prepared,shipped,delivered,cancelled',
            'extra_labels' => 'nullable|array',
            'extra_labels.*' => 'nullable|string|max:100',
            'extra_amounts' => 'nullable|array',
            'extra_amounts.*' => 'nullable|string',
        ]);

        $extraLabels = $request->input('extra_labels', []);
        $extraAmounts = $request->input('extra_amounts', []);
        $operationalExtras = [];

        foreach ($extraLabels as $idx => $label) {
            $label = trim((string) $label);
            $rawAmount = $extraAmounts[$idx] ?? null;
            $normalized = $this->normalizeNominalInput($rawAmount);
            $amount = $normalized !== null ? (float) $normalized : 0;

            if ($label === '' && $amount <= 0) {
                continue;
            }

            $operationalExtras[] = [
                'label' => $label === '' ? 'Operasional Lainnya' : $label,
                'amount' => $amount,
            ];
        }

        $payload = [
            'operational_bensin' => (float) ($validated['operational_bensin'] ?? 0),
            'operational_kuli' => (float) ($validated['operational_kuli'] ?? 0),
            'operational_makan_minum' => (float) ($validated['operational_makan_minum'] ?? 0),
            'operational_listrik' => (float) ($validated['operational_listrik'] ?? 0),
            'operational_wifi' => (float) ($validated['operational_wifi'] ?? 0),
            'admin_note' => $validated['admin_note'] ?? null,
            'drop_date' => $validated['drop_date'] ?? null,
            'shipping_status' => $validated['shipping_status'],
            'operational_extras' => $operationalExtras,
        ];

        $order->update($payload);

        ActivityLogger::log(
            'order.operational_updated',
            'Biaya operasional order #' . $order->id . ' diperbarui',
            $order,
            $payload
        );

        return redirect()->route('admin.orders.index', ['order_type' => 'dapur_sale'])->with('success', 'Biaya operasional order berhasil disimpan.');
    }

    public function invoice(Order $order)
    {
        $user = Auth::user();
        $isPrivileged = in_array($user->role, ['admin', 'superadmin']);
        $isOwner = $user->id === $order->user_id;
        $isSupplier = $user->role === 'supplier';

        if (!$isPrivileged && !$isOwner && !$isSupplier) {
            abort(403);
        }

        $order->load(['user', 'orderItems.product']);

        if ($isSupplier) {
            if ($order->order_type !== 'supplier_purchase') {
                abort(403);
            }

            $supplierItems = $order->orderItems->filter(function ($item) use ($user) {
                return $item->product
                    && $item->product->supplier_id === $user->id
                    && $item->supplier_approved_at !== null;
            });

            if ($supplierItems->isEmpty()) {
                return back()->with('error', 'Nota penjualan muncul setelah item Anda di-ACC.');
            }

            return view('orders.invoice', [
                'order' => $order,
                'invoiceItems' => $supplierItems,
                'useSupplierPrice' => true,
                'viewerRole' => 'supplier',
                'groupedItems' => $supplierItems->groupBy(function ($item) {
                    return $item->product?->supplier?->name ?? 'Supplier';
                }),
            ]);
        }

        $groupedItems = $order->orderItems->groupBy(function ($item) {
            return $item->product?->supplier?->name ?? 'Supplier Tidak Diketahui';
        });

        if ($order->order_type === 'dapur_sale') {
            $rows = $order->orderItems
                ->filter(fn($item) => $item->product)
                ->map(function ($item) use ($order) {
                    $product = $item->product;
                    $unitPrice = (float) ($item->price ?? 0);
                    $quantity = (int) $item->quantity;

                    return [
                        'supplier' => $product->supplier?->name ?? 'Supplier Tidak Diketahui',
                        'item_name' => $product->name,
                        'specification' => $product->description ?: '-',
                        'quantity' => $quantity,
                        'unit' => strtoupper($product->unit ?? 'pcs'),
                        'unit_price' => $unitPrice,
                        'total' => $unitPrice * $quantity,
                        'date_label' => $order->created_at->locale('id')->translatedFormat('l, j F Y'),
                    ];
                })
                ->sortBy(['supplier', 'item_name'])
                ->values();

            return view('orders.dapur_purchase_note', [
                'rows' => $rows,
                'headerTitle' => 'NOTA PEMBELIAN DAPUR',
                'periodLabel' => null,
                'dateTitle' => $order->created_at->locale('id')->translatedFormat('l, j F Y'),
                'dropNote' => $this->buildDropNoteText($order),
                'showPeriodFilter' => false,
                'selectedPeriod' => 'daily',
                'selectedDate' => $order->created_at->toDateString(),
            ]);
        }

        return view('orders.invoice', [
            'order' => $order,
            'invoiceItems' => $order->orderItems,
            'useSupplierPrice' => false,
            'viewerRole' => $user->role,
            'groupedItems' => $groupedItems,
        ]);
    }

    public function supplierDailyInvoice(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            abort(403);
        }

        $date = now()->startOfDay();

        if ($request->filled('date')) {
            try {
                $date = Carbon::createFromFormat('Y-m-d', (string) $request->input('date'))->startOfDay();
            } catch (\Throwable $e) {
                return back()->with('error', 'Format tanggal tidak valid.');
            }
        }

        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        $orders = Order::with(['orderItems.product.supplier'])
            ->where('order_type', 'supplier_purchase')
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['processed', 'completed'])
            ->get();

        $approvedItems = $orders->flatMap(function ($order) {
            return $order->orderItems->filter(fn($item) => $item->supplier_approved_at !== null)->map(function ($item) use ($order) {
                $item->order_number = $order->id;
                return $item;
            });
        });

        $groupedBySupplier = $approvedItems->groupBy(function ($item) {
            return $item->product?->supplier?->name ?? 'Supplier Tidak Diketahui';
        });

        return view('orders.supplier_daily_invoice', [
            'date' => $date,
            'groupedBySupplier' => $groupedBySupplier,
            'total' => $approvedItems->sum(function ($item) {
                return (float) $item->price * (int) $item->quantity;
            }),
        ]);
    }

    public function dapurPurchaseNoteByPeriod(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            abort(403);
        }

        $period = in_array($request->input('period'), ['daily', 'weekly', 'monthly'], true)
            ? $request->input('period')
            : 'daily';

        $baseDate = now();
        if ($request->filled('date')) {
            try {
                $baseDate = Carbon::createFromFormat('Y-m-d', (string) $request->input('date'));
            } catch (\Throwable $e) {
                return back()->with('error', 'Format tanggal tidak valid.');
            }
        }

        [$start, $end, $periodLabel] = $this->resolveDapurNoteRange($period, $baseDate);

        $items = OrderItem::query()
            ->with(['product.supplier', 'order'])
            ->whereHas('order', function ($query) use ($start, $end) {
                $query->where('order_type', 'dapur_sale')
                    ->whereIn('status', ['processed', 'completed'])
                    ->whereBetween('created_at', [$start, $end]);
            })
            ->get();

        $rows = $items
            ->filter(fn($item) => $item->product && $item->order)
            ->groupBy(function ($item) {
                $product = $item->product;
                return implode('|', [
                    $product->supplier?->name ?? 'Supplier Tidak Diketahui',
                    $product->name,
                    strtoupper($product->unit ?? 'PCS'),
                    (string) ((float) ($item->price ?? 0)),
                ]);
            })
            ->map(function ($group) use ($periodLabel) {
                $first = $group->first();
                $product = $first->product;
                $unitPrice = (float) ($first->price ?? 0);
                $quantity = (int) $group->sum('quantity');

                return [
                    'supplier' => $product->supplier?->name ?? 'Supplier Tidak Diketahui',
                    'item_name' => $product->name,
                    'specification' => $product->description ?: '-',
                    'quantity' => $quantity,
                    'unit' => strtoupper($product->unit ?? 'pcs'),
                    'unit_price' => $unitPrice,
                    'total' => $unitPrice * $quantity,
                    'date_label' => $periodLabel,
                ];
            })
            ->sortBy(['supplier', 'item_name'])
            ->values();

        return view('orders.dapur_purchase_note', [
            'rows' => $rows,
            'headerTitle' => 'NOTA PEMBELIAN DAPUR',
            'periodLabel' => null,
            'dateTitle' => $baseDate->locale('id')->translatedFormat('l, j F Y'),
            'dropNote' => null,
            'showPeriodFilter' => true,
            'selectedPeriod' => $period,
            'selectedDate' => $baseDate->toDateString(),
        ]);
    }

    private function resolveDapurNoteRange(string $period, Carbon $baseDate): array
    {
        if ($period === 'weekly') {
            $start = $baseDate->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
            $end = $baseDate->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

            return [$start, $end, 'Mingguan (' . $start->locale('id')->translatedFormat('j M Y') . ' - ' . $end->locale('id')->translatedFormat('j M Y') . ')'];
        }

        if ($period === 'monthly') {
            $start = $baseDate->copy()->startOfMonth()->startOfDay();
            $end = $baseDate->copy()->endOfMonth()->endOfDay();

            return [$start, $end, 'Bulanan (' . $start->locale('id')->translatedFormat('F Y') . ')'];
        }

        $start = $baseDate->copy()->startOfDay();
        $end = $baseDate->copy()->endOfDay();

        return [$start, $end, 'Harian (' . $start->locale('id')->translatedFormat('j F Y') . ')'];
    }

    private function buildDropNoteText(Order $order): ?string
    {
        if (!$order->drop_date) {
            return null;
        }

        return 'Drop pada Hari ' . Carbon::parse($order->drop_date)->locale('id')->translatedFormat('l, j F Y');
    }

    private function normalizeNominalInput($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        if (preg_match('/^\d{1,3}(\.\d{3})+$/', $raw)) {
            $clean = str_replace('.', '', $raw);
            return $clean === '' ? null : $clean;
        }

        if (preg_match('/^\d{1,3}(,\d{3})+$/', $raw)) {
            $clean = str_replace(',', '', $raw);
            return $clean === '' ? null : $clean;
        }

        if (preg_match('/^\d+[\.,]\d+$/', $raw)) {
            $number = (float) str_replace(',', '.', $raw);
            return (string) round($number);
        }

        $clean = preg_replace('/[^0-9]/', '', $raw);

        return $clean === '' ? null : $clean;
    }
}
