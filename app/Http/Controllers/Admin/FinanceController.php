<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PayrollDistribution;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        [$start, $end, $periodLabel, $periodType] = $this->resolvePeriod($request);

        $orderQuery = Order::query()
            ->where('order_type', 'dapur_sale')
            ->whereIn('status', ['processed', 'completed'])
            ->whereBetween('created_at', [$start, $end]);

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

        $profitRows = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.order_type', 'dapur_sale')
            ->whereIn('orders.status', ['processed', 'completed'])
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('order_items.product_id', 'products.name', 'products.unit')
            ->selectRaw('products.name as product_name')
            ->selectRaw('products.unit as unit')
            ->selectRaw('SUM(order_items.quantity) as qty')
            ->selectRaw('SUM(order_items.quantity * COALESCE(products.supplier_price, 0)) as total_supplier')
            ->selectRaw('SUM(order_items.quantity * order_items.price) as total_dapur')
            ->orderBy('products.name')
            ->get()
            ->map(function ($row) {
                $qty = (int) ($row->qty ?? 0);
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
            });

        $distributions = PayrollDistribution::with('creator')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.finance.index', compact(
            'start',
            'end',
            'periodType',
            'periodLabel',
            'hargaBeliDapur',
            'hargaBeliSupplier',
            'labaKotor',
            'profitRows',
            'distributions'
        ));
    }

    public function exportSpreadsheet(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        [$start, $end, $periodLabel] = $this->resolvePeriod($request);
        $supplierSections = $this->buildSupplierSections($start, $end);

        $filename = 'laporan-laba-supplier-' . strtolower($periodLabel) . '-' . now()->format('Ymd-His') . '.xls';

        return response()
            ->view('reports.supplier_profit_export', [
                'title' => 'Laporan Laba Supplier',
                'periodLabel' => $periodLabel,
                'start' => $start,
                'end' => $end,
                'supplierSections' => $supplierSections,
            ])
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function storeDistribution(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $request->merge([
            'pendapatan_total' => $this->normalizeNominalInput($request->input('pendapatan_total')),
            'gaji_internal_kepala_dapur' => $this->normalizeNominalInput($request->input('gaji_internal_kepala_dapur')),
            'gaji_internal_asisten_lapangan' => $this->normalizeNominalInput($request->input('gaji_internal_asisten_lapangan')),
            'gaji_internal_ahli_gizi' => $this->normalizeNominalInput($request->input('gaji_internal_ahli_gizi')),
            'gaji_internal_akuntan' => $this->normalizeNominalInput($request->input('gaji_internal_akuntan')),
            'gaji_eksternal_kodim' => $this->normalizeNominalInput($request->input('gaji_eksternal_kodim')),
            'gaji_eksternal_koramil' => $this->normalizeNominalInput($request->input('gaji_eksternal_koramil')),
        ]);

        $validated = $request->validate([
            'period_type' => 'required|in:daily,weekly,monthly,manual',
            'period_label' => 'nullable|string|max:100',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
            'pendapatan_total' => 'required|numeric|min:0',
            'gaji_internal_kepala_dapur' => 'required|numeric|min:0',
            'gaji_internal_asisten_lapangan' => 'required|numeric|min:0',
            'gaji_internal_ahli_gizi' => 'required|numeric|min:0',
            'gaji_internal_akuntan' => 'required|numeric|min:0',
            'gaji_eksternal_kodim' => 'required|numeric|min:0',
            'gaji_eksternal_koramil' => 'required|numeric|min:0',
            'kepala_dapur_percent' => 'required|numeric|min:0|max:100',
            'staff_1_percent' => 'required|numeric|min:0|max:100',
            'staff_2_percent' => 'required|numeric|min:0|max:100',
            'staff_3_percent' => 'required|numeric|min:0|max:100',
            'staff_4_percent' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $totalStaffPercent = (float) $validated['staff_1_percent']
            + (float) $validated['staff_2_percent']
            + (float) $validated['staff_3_percent']
            + (float) $validated['staff_4_percent'];

        if (abs($totalStaffPercent - 100) > 0.0001) {
            return back()
                ->withErrors(['staff_4_percent' => 'Total persen staf 1-4 harus tepat 100%.'])
                ->withInput();
        }

        $pendapatan = (float) $validated['pendapatan_total'];
        $gajiInternalKepalaDapur = (float) $validated['gaji_internal_kepala_dapur'];
        $gajiInternalAsistenLapangan = (float) $validated['gaji_internal_asisten_lapangan'];
        $gajiInternalAhliGizi = (float) $validated['gaji_internal_ahli_gizi'];
        $gajiInternalAkuntan = (float) $validated['gaji_internal_akuntan'];
        $gajiInternal = $gajiInternalKepalaDapur
            + $gajiInternalAsistenLapangan
            + $gajiInternalAhliGizi
            + $gajiInternalAkuntan;
        $gajiEksternalKodim = (float) $validated['gaji_eksternal_kodim'];
        $gajiEksternalKoramil = (float) $validated['gaji_eksternal_koramil'];
        $gajiEksternal = $gajiEksternalKodim + $gajiEksternalKoramil;
        $totalGaji = $gajiInternal + $gajiEksternal;
        $sisaSetelahGaji = $pendapatan - $totalGaji;

        $kepalaPercent = (float) $validated['kepala_dapur_percent'];
        $kepalaNominal = $sisaSetelahGaji * ($kepalaPercent / 100);
        $sisaUntukStaf = $sisaSetelahGaji - $kepalaNominal;

        $staff1Nominal = $sisaUntukStaf * (((float) $validated['staff_1_percent']) / 100);
        $staff2Nominal = $sisaUntukStaf * (((float) $validated['staff_2_percent']) / 100);
        $staff3Nominal = $sisaUntukStaf * (((float) $validated['staff_3_percent']) / 100);
        $staff4Nominal = $sisaUntukStaf * (((float) $validated['staff_4_percent']) / 100);

        $distribution = PayrollDistribution::create([
            'created_by' => Auth::id(),
            'period_type' => $validated['period_type'],
            'period_label' => $validated['period_label'] ?? null,
            'period_start' => $validated['period_start'] ?? null,
            'period_end' => $validated['period_end'] ?? null,
            'pendapatan_total' => $pendapatan,
            'gaji_internal_kepala_dapur' => $gajiInternalKepalaDapur,
            'gaji_internal_asisten_lapangan' => $gajiInternalAsistenLapangan,
            'gaji_internal_ahli_gizi' => $gajiInternalAhliGizi,
            'gaji_internal_akuntan' => $gajiInternalAkuntan,
            'gaji_internal' => $gajiInternal,
            'gaji_eksternal_kodim' => $gajiEksternalKodim,
            'gaji_eksternal_koramil' => $gajiEksternalKoramil,
            'gaji_eksternal' => $gajiEksternal,
            'total_gaji' => $totalGaji,
            'sisa_setelah_gaji' => $sisaSetelahGaji,
            'kepala_dapur_percent' => $kepalaPercent,
            'kepala_dapur_nominal' => $kepalaNominal,
            'sisa_untuk_staf' => $sisaUntukStaf,
            'staff_1_name' => 'Pak Suci',
            'staff_2_name' => 'Bu Suci',
            'staff_3_name' => 'Pak Juki',
            'staff_4_name' => 'Pak Kholik',
            'staff_1_percent' => (float) $validated['staff_1_percent'],
            'staff_2_percent' => (float) $validated['staff_2_percent'],
            'staff_3_percent' => (float) $validated['staff_3_percent'],
            'staff_4_percent' => (float) $validated['staff_4_percent'],
            'staff_1_nominal' => $staff1Nominal,
            'staff_2_nominal' => $staff2Nominal,
            'staff_3_nominal' => $staff3Nominal,
            'staff_4_nominal' => $staff4Nominal,
            'notes' => $validated['notes'] ?? null,
        ]);

        ActivityLogger::log(
            'finance.salary_distribution_created',
            'Admin membuat pembagian gaji periode ' . ($distribution->period_label ?? strtoupper($distribution->period_type)),
            $distribution,
            [
                'pendapatan_total' => $distribution->pendapatan_total,
                'gaji_internal' => $distribution->gaji_internal,
                'gaji_eksternal_kodim' => $distribution->gaji_eksternal_kodim,
                'gaji_eksternal_koramil' => $distribution->gaji_eksternal_koramil,
                'total_gaji' => $distribution->total_gaji,
                'sisa_setelah_gaji' => $distribution->sisa_setelah_gaji,
            ]
        );

        return redirect()
            ->route('admin.finance.index')
            ->with('success', 'Pembagian gaji berhasil dihitung dan disimpan.');
    }

    private function resolvePeriod(Request $request): array
    {
        $period = (string) $request->get('period', 'monthly');
        $now = Carbon::now();

        if ($period === 'daily') {
            return [
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay(),
                'Harian',
                'daily',
            ];
        }

        if ($period === 'weekly') {
            return [
                $now->copy()->startOfWeek(Carbon::MONDAY),
                $now->copy()->endOfWeek(Carbon::SUNDAY),
                'Mingguan',
                'weekly',
            ];
        }

        return [
            $now->copy()->startOfMonth(),
            $now->copy()->endOfMonth(),
            'Bulanan',
            'monthly',
        ];
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
