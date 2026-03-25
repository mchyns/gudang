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
        $completedOrders = Order::whereIn('status', ['processed', 'completed']);
        $hargaBeliDapur = (float) $completedOrders->sum('total_price');

        $hargaBeliSupplier = (float) OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->whereIn('orders.status', ['processed', 'completed'])
            ->selectRaw('SUM(order_items.quantity * products.supplier_price) as total_supplier')
            ->value('total_supplier');

        $operasionalTotal = (float) Order::query()
            ->whereIn('status', ['processed', 'completed'])
            ->selectRaw('SUM(operational_bensin + operational_kuli + operational_makan_minum + operational_listrik + operational_wifi) as total_operasional')
            ->value('total_operasional');

        // Sesuai rumus yang diminta user
        $labaKotor = ($hargaBeliSupplier + $operasionalTotal) - $hargaBeliDapur;

        $latestOrders = Order::with(['user', 'orderItems.product'])
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
            'operasionalTotal',
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
            ->whereIn('status', ['processed', 'completed'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        $hargaBeliDapur = (float) $orders->sum('total_price');

        $hargaBeliSupplier = (float) OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->whereIn('orders.status', ['processed', 'completed'])
            ->whereBetween('orders.created_at', [$start, $end])
            ->selectRaw('SUM(order_items.quantity * products.supplier_price) as total_supplier')
            ->value('total_supplier');

        $operasionalTotal = (float) Order::query()
            ->whereIn('status', ['processed', 'completed'])
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('SUM(operational_bensin + operational_kuli + operational_makan_minum + operational_listrik + operational_wifi) as total_operasional')
            ->value('total_operasional');

        $labaKotor = ($hargaBeliSupplier + $operasionalTotal) - $hargaBeliDapur;

        return view('superadmin.print_invoice_period', compact(
            'period',
            'label',
            'start',
            'end',
            'orders',
            'hargaBeliDapur',
            'hargaBeliSupplier',
            'operasionalTotal',
            'labaKotor'
        ));
    }
}
