<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PayrollDistribution;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InsightController extends Controller
{
    public function index(Request $request)
    {
        $hargaBeliDapur = (float) OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.order_type', 'dapur_sale')
            ->whereIn('orders.status', ['processed', 'completed'])
            ->selectRaw('SUM(order_items.quantity * order_items.price) as total_dapur')
            ->value('total_dapur');

        $hargaBeliSupplier = (float) OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.order_type', 'dapur_sale')
            ->whereIn('orders.status', ['processed', 'completed'])
            ->selectRaw('SUM(order_items.quantity * products.supplier_price) as total_supplier')
            ->value('total_supplier');

        $labaKotor = $hargaBeliDapur - $hargaBeliSupplier;

        $latestOrders = Order::with(['user', 'orderItems.product'])
            ->where('order_type', 'dapur_sale')
            ->latest()
            ->take(10)
            ->get();

        $dapurPartners = User::where('role', 'dapur')->withCount('orders')->get();
        $suppliers = User::where('role', 'supplier')->withCount('suppliedProducts')->get();
        $priceSnapshots = Product::with(['supplier', 'category'])->latest()->take(20)->get();
        $availableMonths = PayrollDistribution::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key")
            ->whereNotNull('created_at')
            ->distinct()
            ->orderByDesc('month_key')
            ->pluck('month_key');

        $selectedMonth = (string) $request->get('bulan', '');
        $selectedMonth = in_array($selectedMonth, $availableMonths->all(), true) ? $selectedMonth : '';

        $payrollQuery = PayrollDistribution::with('creator')
            ->when($selectedMonth !== '', function ($query) use ($selectedMonth) {
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$selectedMonth]);
            });

        $latestPayrolls = (clone $payrollQuery)->latest()->take(30)->get();

        $totalDistribusiPendapatan = (float) (clone $payrollQuery)->sum('pendapatan_total');
        $totalDistribusiGaji = (float) (clone $payrollQuery)->sum('total_gaji');
        $totalDistribusiPakUed = (float) (clone $payrollQuery)->sum('kepala_dapur_nominal');
        $totalDistribusiStaf = (float) (clone $payrollQuery)->sum('sisa_untuk_staf');

        $monthlyPayrollSummaries = PayrollDistribution::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key")
            ->selectRaw('SUM(pendapatan_total) as total_pendapatan')
            ->selectRaw('SUM(gaji_internal) as total_internal')
            ->selectRaw('SUM(gaji_eksternal) as total_eksternal')
            ->selectRaw('SUM(total_gaji) as total_gaji')
            ->selectRaw('SUM(kepala_dapur_nominal) as total_pak_ued')
            ->selectRaw('SUM(sisa_untuk_staf) as total_staf')
            ->groupBy('month_key')
            ->orderByDesc('month_key')
            ->get();

        return view('superadmin.insights', compact(
            'hargaBeliDapur',
            'hargaBeliSupplier',
            'labaKotor',
            'latestOrders',
            'dapurPartners',
            'suppliers',
            'priceSnapshots',
            'latestPayrolls',
            'availableMonths',
            'selectedMonth',
            'totalDistribusiPendapatan',
            'totalDistribusiGaji',
            'totalDistribusiPakUed',
            'totalDistribusiStaf',
            'monthlyPayrollSummaries'
        ));
    }

    public function printPeriod(string $period)
    {
        $now = Carbon::now();

        if ($period === 'daily') {
            $start = $now->copy()->startOfDay();
            $end = $now->copy()->endOfDay();
            $label = 'Harian';
        } elseif ($period === 'weekly') {
            $start = $now->copy()->startOfWeek(Carbon::MONDAY);
            $end = $now->copy()->endOfWeek(Carbon::SUNDAY);
            $label = 'Mingguan';
        } else {
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfMonth();
            $label = 'Bulanan';
        }

        $orders = Order::with(['user', 'orderItems.product'])
            ->where('order_type', 'dapur_sale')
            ->whereIn('status', ['processed', 'completed'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        $hargaBeliDapur = (float) OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.order_type', 'dapur_sale')
            ->whereIn('orders.status', ['processed', 'completed'])
            ->whereBetween('orders.created_at', [$start, $end])
            ->selectRaw('SUM(order_items.quantity * order_items.price) as total_dapur')
            ->value('total_dapur');

        $hargaBeliSupplier = (float) OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.order_type', 'dapur_sale')
            ->whereIn('orders.status', ['processed', 'completed'])
            ->whereBetween('orders.created_at', [$start, $end])
            ->selectRaw('SUM(order_items.quantity * products.supplier_price) as total_supplier')
            ->value('total_supplier');

        $labaKotor = $hargaBeliDapur - $hargaBeliSupplier;

        return view('superadmin.print_invoice_period', compact(
            'period',
            'label',
            'start',
            'end',
            'orders',
            'hargaBeliDapur',
            'hargaBeliSupplier',
            'labaKotor'
        ));
    }

    public function exportSpreadsheet(string $period)
    {
        $now = Carbon::now();

        if ($period === 'daily') {
            $start = $now->copy()->startOfDay();
            $end = $now->copy()->endOfDay();
            $label = 'Harian';
        } elseif ($period === 'weekly') {
            $start = $now->copy()->startOfWeek(Carbon::MONDAY);
            $end = $now->copy()->endOfWeek(Carbon::SUNDAY);
            $label = 'Mingguan';
        } else {
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfMonth();
            $label = 'Bulanan';
        }

        $supplierSections = $this->buildSupplierSections($start, $end);
        $filename = 'laporan-laba-supplier-' . strtolower($label) . '-' . now()->format('Ymd-His') . '.xls';

        return response()
            ->view('reports.supplier_profit_export', [
                'title' => 'Laporan Laba Supplier',
                'periodLabel' => $label,
                'start' => $start,
                'end' => $end,
                'supplierSections' => $supplierSections,
            ])
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function buildSupplierSections(Carbon $start, Carbon $end): array
    {
        $rows = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('users as suppliers', 'suppliers.id', '=', 'products.supplier_id')
            ->where('orders.order_type', 'dapur_sale')
            ->whereIn('orders.status', ['processed', 'completed'])
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('products.supplier_id', 'suppliers.name', 'order_items.product_id', 'products.name', 'products.unit')
            ->selectRaw('products.supplier_id as supplier_id')
            ->selectRaw('COALESCE(suppliers.name, "Tanpa Supplier") as supplier_name')
            ->selectRaw('products.name as product_name')
            ->selectRaw('products.unit as unit')
            ->selectRaw('SUM(order_items.quantity) as qty')
            ->selectRaw('SUM(order_items.quantity * COALESCE(products.supplier_price, 0)) as total_supplier')
            ->selectRaw('SUM(order_items.quantity * order_items.price) as total_dapur')
            ->orderBy('supplier_name')
            ->orderBy('product_name')
            ->get();

        return $rows
            ->groupBy(function ($row) {
                return ($row->supplier_id ?? 0) . '|' . $row->supplier_name;
            })
            ->map(function ($group) {
                $first = $group->first();

                $items = $group->map(function ($row) {
                    $qty = (float) ($row->qty ?? 0);
                    $totalSupplier = (float) ($row->total_supplier ?? 0);
                    $totalDapur = (float) ($row->total_dapur ?? 0);

                    return [
                        'product_name' => $row->product_name,
                        'unit' => $row->unit ?: 'pcs',
                        'qty' => $qty,
                        'harga_supplier' => $qty > 0 ? ($totalSupplier / $qty) : 0,
                        'total_supplier' => $totalSupplier,
                        'harga_dapur' => $qty > 0 ? ($totalDapur / $qty) : 0,
                        'total_dapur' => $totalDapur,
                        'laba' => $totalDapur - $totalSupplier,
                    ];
                })->values()->all();

                return [
                    'supplier_name' => $first->supplier_name,
                    'rows' => $items,
                ];
            })
            ->values()
            ->all();
    }
}
