<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Services\ActivityLogger;
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
        // Admin or Superadmin
        $role = Auth::user()->role;
        $orders = collect();

        if ($role === 'superadmin') {
            $orders = Order::with(['user', 'orderItems.product'])->latest()->get();
        } else {
            // Admin default view: Pending orders at top
            $orders = Order::with(['user', 'orderItems.product'])
                        ->orderByRaw("FIELD(status, 'pending', 'processed', 'completed', 'cancelled')")
                        ->latest()
                        ->get();
        }

        return view('orders.index', compact('orders'));
    }

    /**
     * Display a listing for Dapur (My Orders).
     */
    public function dapurIndex()
    {
        $orders = Order::where('user_id', Auth::id())
                    ->with(['orderItems.product'])
                    ->latest()
                    ->get();

        return view('orders.dapur_index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource (Kitchen Checkout).
     */
    public function create(Request $request)
    {
        $products = Product::with('category')
            ->where('status', 'active')
            ->whereNotNull('price')
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
                'total_price' => 0, // Will update below
                'status' => 'pending',
                'note' => $request->note ?? '-'
            ]);

            $totalPrice = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check stock
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi (Sisa: {$product->stock})");
                }

                // Deduct stock immediately? Or on approval? 
                // Let's deduct on approval/process to be safe, OR deduct now to reserve.
                // Deduct now to reserve is better for UX.
                $product->decrement('stock', $item['quantity']);

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

        // If cancelling, restore stock
        if ($validated['status'] === 'cancelled' && $order->status !== 'cancelled') {
            foreach ($order->orderItems as $item) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        $order->update(['status' => $validated['status']]);

        ActivityLogger::log(
            'order.status_updated',
            'Status order #' . $order->id . ' diubah menjadi ' . $validated['status'],
            $order,
            ['status' => $validated['status']]
        );

        return redirect()->route('admin.orders.index')->with('success', "Order #{$order->id} status updated to " . ucfirst($validated['status']));
    }

    public function editOperational(Order $order)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
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

        $validated = $request->validate([
            'operational_bensin' => 'nullable|numeric|min:0',
            'operational_kuli' => 'nullable|numeric|min:0',
            'operational_makan_minum' => 'nullable|numeric|min:0',
            'operational_listrik' => 'nullable|numeric|min:0',
            'operational_wifi' => 'nullable|numeric|min:0',
        ]);

        $payload = [
            'operational_bensin' => (float) ($validated['operational_bensin'] ?? 0),
            'operational_kuli' => (float) ($validated['operational_kuli'] ?? 0),
            'operational_makan_minum' => (float) ($validated['operational_makan_minum'] ?? 0),
            'operational_listrik' => (float) ($validated['operational_listrik'] ?? 0),
            'operational_wifi' => (float) ($validated['operational_wifi'] ?? 0),
        ];

        $order->update($payload);

        ActivityLogger::log(
            'order.operational_updated',
            'Biaya operasional order #' . $order->id . ' diperbarui',
            $order,
            $payload
        );

        return redirect()->route('admin.orders.index')->with('success', 'Biaya operasional order berhasil disimpan.');
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
            $supplierItems = $order->orderItems->filter(function ($item) use ($user) {
                return $item->product && $item->product->supplier_id === $user->id;
            });

            if ($supplierItems->isEmpty()) {
                abort(403);
            }

            return view('orders.invoice', [
                'order' => $order,
                'invoiceItems' => $supplierItems,
                'useSupplierPrice' => true,
                'viewerRole' => 'supplier',
            ]);
        }

        return view('orders.invoice', [
            'order' => $order,
            'invoiceItems' => $order->orderItems,
            'useSupplierPrice' => false,
            'viewerRole' => $user->role,
        ]);
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
