<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Insight Superadmin</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="ui-panel p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Cetak Nota Periode</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('superadmin.insights.print', 'daily') }}" target="_blank" class="ui-btn-primary">Cetak Harian</a>
                    <a href="{{ route('superadmin.insights.print', 'weekly') }}" target="_blank" class="px-4 py-2 bg-emerald-600 text-white rounded text-sm hover:bg-emerald-700">Cetak Mingguan</a>
                    <a href="{{ route('superadmin.insights.print', 'monthly') }}" target="_blank" class="px-4 py-2 bg-amber-600 text-white rounded text-sm hover:bg-amber-700">Cetak Bulanan</a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="ui-panel p-5">
                    <p class="text-xs uppercase text-gray-500">Harga Beli Supplier (H.B.S)</p>
                    <p class="text-2xl font-bold text-amber-700 mt-1">Rp {{ number_format($hargaBeliSupplier, 0, ',', '.') }}</p>
                </div>
                <div class="ui-panel p-5">
                    <p class="text-xs uppercase text-gray-500">Total Operasional</p>
                    <p class="text-2xl font-bold text-indigo-700 mt-1">Rp {{ number_format($operasionalTotal, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white border rounded-lg p-5">
                    <p class="text-xs uppercase text-gray-500">Harga Beli Dapur</p>
                    <p class="text-2xl font-bold text-green-700 mt-1">Rp {{ number_format($hargaBeliDapur, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="ui-panel p-5">
                <h3 class="font-semibold text-gray-800 mb-2">Rumus Laba Kotor</h3>
                <p class="text-sm text-gray-600 mb-2">Laba kotor = Harga beli supplier + Operasional - Harga beli dapur</p>
                <p class="text-lg font-bold {{ $labaKotor >= 0 ? 'text-indigo-700' : 'text-red-700' }}">Rp {{ number_format($labaKotor, 0, ',', '.') }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white border rounded-lg p-5">
                    <h3 class="font-semibold text-gray-800 mb-3">Mitra Dapur Tergabung</h3>
                    <div class="space-y-2 max-h-80 overflow-auto">
                        @forelse($dapurPartners as $partner)
                            <div class="flex justify-between text-sm border-b pb-2">
                                <span>{{ $partner->name }}</span>
                                <span class="text-gray-500">Order: {{ $partner->orders_count }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada mitra dapur.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white border rounded-lg p-5">
                    <h3 class="font-semibold text-gray-800 mb-3">Supplier Tergabung</h3>
                    <div class="space-y-2 max-h-80 overflow-auto">
                        @forelse($suppliers as $supplier)
                            <div class="flex justify-between text-sm border-b pb-2">
                                <span>{{ $supplier->name }}</span>
                                <span class="text-gray-500">Produk: {{ $supplier->supplied_products_count }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada supplier.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="ui-panel p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Snapshot Harga Supplier vs Harga Fix Admin</h3>
                <div class="ui-table-wrap">
                    <table class="min-w-full text-sm ui-table">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left">Produk</th>
                                <th class="px-3 py-2 text-left">Supplier</th>
                                <th class="px-3 py-2 text-left">Kategori</th>
                                <th class="px-3 py-2 text-left">Harga Supplier</th>
                                <th class="px-3 py-2 text-left">Harga Fix</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($priceSnapshots as $product)
                                <tr>
                                    <td class="px-3 py-2">{{ $product->name }}</td>
                                    <td class="px-3 py-2">{{ $product->supplier->name ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $product->category->name ?? '-' }}</td>
                                    <td class="px-3 py-2">Rp {{ number_format($product->supplier_price, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 font-semibold text-indigo-700">{{ $product->price ? 'Rp ' . number_format($product->price, 0, ',', '.') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="ui-panel p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Pantauan Nota Order Terbaru</h3>
                <div class="ui-table-wrap">
                    <table class="min-w-full text-sm ui-table">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left">Order</th>
                                <th class="px-3 py-2 text-left">Dapur</th>
                                <th class="px-3 py-2 text-left">Harga Supplier</th>
                                <th class="px-3 py-2 text-left">Operasional</th>
                                <th class="px-3 py-2 text-left">Harga Dapur</th>
                                <th class="px-3 py-2 text-left">Laba Kotor</th>
                                <th class="px-3 py-2 text-left">Nota</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($latestOrders as $order)
                                @php
                                    $supplierTotalPerOrder = $order->orderItems->sum(function ($item) {
                                        return ($item->quantity ?? 0) * ($item->product->supplier_price ?? 0);
                                    });
                                    $opsPerOrder = (float) (
                                        ($order->operational_bensin ?? 0)
                                        + ($order->operational_kuli ?? 0)
                                        + ($order->operational_makan_minum ?? 0)
                                        + ($order->operational_listrik ?? 0)
                                        + ($order->operational_wifi ?? 0)
                                    );
                                    $opsPerOrder += collect($order->operational_extras ?? [])->sum(function ($extra) {
                                        return (float) ($extra['amount'] ?? 0);
                                    });
                                    $grossPerOrder = ($supplierTotalPerOrder + $opsPerOrder) - (float) $order->total_price;
                                @endphp
                                <tr>
                                    <td class="px-3 py-2">#{{ $order->id }}</td>
                                    <td class="px-3 py-2">{{ $order->user->name ?? '-' }}</td>
                                    <td class="px-3 py-2">Rp {{ number_format($supplierTotalPerOrder, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2">Rp {{ number_format($opsPerOrder, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 {{ $grossPerOrder >= 0 ? 'text-indigo-700' : 'text-red-700' }} font-semibold">Rp {{ number_format($grossPerOrder, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2"><a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800">Cetak</a></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-4 text-center text-gray-500">Belum ada order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="ui-panel p-5">
                <h3 class="font-semibold text-gray-800 mb-1">Transparansi Pembagian Gaji</h3>
                <p class="text-sm text-gray-500 mb-4">Super Admin hanya memantau hasil pembagian, tanpa akses input atau ubah data.</p>

                <form method="GET" action="{{ route('superadmin.insights.index') }}" class="mb-4 flex flex-wrap items-end gap-2 ui-panel-soft p-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Filter Bulan</label>
                        <select name="bulan" class="border-gray-300 rounded-md text-sm min-w-[180px]">
                            <option value="">Semua Bulan</option>
                            @foreach($availableMonths as $month)
                                <option value="{{ $month }}" @selected($selectedMonth === $month)>
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button class="ui-btn-primary">Terapkan</button>
                    <a href="{{ route('superadmin.insights.index') }}" class="ui-btn-ghost">Reset</a>
                </form>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div class="bg-gray-50 border rounded p-3">
                        <p class="text-xs uppercase text-gray-500">Total Pendapatan Dibagi</p>
                        <p class="text-base font-bold text-gray-800">Rp {{ number_format($totalDistribusiPendapatan, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gray-50 border rounded p-3">
                        <p class="text-xs uppercase text-gray-500">Total Gaji</p>
                        <p class="text-base font-bold text-gray-800">Rp {{ number_format($totalDistribusiGaji, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gray-50 border rounded p-3">
                        <p class="text-xs uppercase text-gray-500">Total Pak Ued</p>
                        <p class="text-base font-bold text-gray-800">Rp {{ number_format($totalDistribusiPakUed, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gray-50 border rounded p-3">
                        <p class="text-xs uppercase text-gray-500">Total Bagian Staf</p>
                        <p class="text-base font-bold text-gray-800">Rp {{ number_format($totalDistribusiStaf, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="ui-table-wrap mb-5">
                    <h4 class="font-semibold text-gray-800 mb-2">Ringkasan Pembagian Per Bulan</h4>
                    <table class="min-w-full text-sm ui-table">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left">Bulan</th>
                                <th class="px-3 py-2 text-left">Pendapatan</th>
                                <th class="px-3 py-2 text-left">Internal</th>
                                <th class="px-3 py-2 text-left">Eksternal</th>
                                <th class="px-3 py-2 text-left">Total Gaji</th>
                                <th class="px-3 py-2 text-left">Pak Ued</th>
                                <th class="px-3 py-2 text-left">Staf</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($monthlyPayrollSummaries as $summary)
                                <tr>
                                    <td class="px-3 py-2">{{ \Carbon\Carbon::createFromFormat('Y-m', $summary->month_key)->translatedFormat('F Y') }}</td>
                                    <td class="px-3 py-2">Rp {{ number_format($summary->total_pendapatan, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2">Rp {{ number_format($summary->total_internal, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2">Rp {{ number_format($summary->total_eksternal, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2">Rp {{ number_format($summary->total_gaji, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2">Rp {{ number_format($summary->total_pak_ued, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2">Rp {{ number_format($summary->total_staf, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-4 text-center text-gray-500">Belum ada data bulanan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="ui-table-wrap">
                    <table class="min-w-full text-sm ui-table">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left whitespace-nowrap">Waktu</th>
                                <th class="px-3 py-2 text-left">Periode</th>
                                <th class="px-3 py-2 text-left whitespace-nowrap">Pendapatan</th>
                                <th class="px-3 py-2 text-left whitespace-nowrap">Total Gaji</th>
                                <th class="px-3 py-2 text-left whitespace-nowrap">Sisa</th>
                                <th class="px-3 py-2 text-left whitespace-nowrap">Pak Ued</th>
                                <th class="px-3 py-2 text-left">Disimpan Oleh</th>
                                <th class="px-3 py-2 text-left">Rincian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($latestPayrolls as $dist)
                                <tr class="align-top">
                                    <td class="px-3 py-3 whitespace-nowrap">{{ $dist->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-3 py-3">{{ $dist->period_label ?? strtoupper($dist->period_type) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Rp {{ number_format($dist->pendapatan_total, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Rp {{ number_format($dist->total_gaji, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap font-semibold {{ $dist->sisa_setelah_gaji >= 0 ? 'text-emerald-700' : 'text-red-700' }}">Rp {{ number_format($dist->sisa_setelah_gaji, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Rp {{ number_format($dist->kepala_dapur_nominal, 0, ',', '.') }} <span class="text-gray-500">({{ number_format($dist->kepala_dapur_percent, 2) }}%)</span></td>
                                    <td class="px-3 py-3">{{ $dist->creator->name ?? '-' }}</td>
                                    <td class="px-3 py-3">
                                        <details class="group">
                                            <summary class="cursor-pointer text-indigo-600 hover:text-indigo-800">Lihat Rincian</summary>
                                            <div class="mt-2 p-3 bg-gray-50 border rounded-lg space-y-3 text-xs leading-5 min-w-[360px]">
                                                <div>
                                                    <p class="font-semibold text-gray-700 mb-1">Internal</p>
                                                    <p>Kepala Dapur: Rp {{ number_format($dist->gaji_internal_kepala_dapur ?? 0, 0, ',', '.') }}</p>
                                                    <p>Asisten Lapangan: Rp {{ number_format($dist->gaji_internal_asisten_lapangan ?? 0, 0, ',', '.') }}</p>
                                                    <p>Ahli Gizi: Rp {{ number_format($dist->gaji_internal_ahli_gizi ?? 0, 0, ',', '.') }}</p>
                                                    <p>Akuntan: Rp {{ number_format($dist->gaji_internal_akuntan ?? 0, 0, ',', '.') }}</p>
                                                    <p class="font-semibold">Total Internal: Rp {{ number_format($dist->gaji_internal ?? 0, 0, ',', '.') }}</p>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-700 mb-1">Eksternal</p>
                                                    <p>Kodim: Rp {{ number_format($dist->gaji_eksternal_kodim ?? 0, 0, ',', '.') }}</p>
                                                    <p>Koramil: Rp {{ number_format($dist->gaji_eksternal_koramil ?? 0, 0, ',', '.') }}</p>
                                                    <p class="font-semibold">Total Eksternal: Rp {{ number_format($dist->gaji_eksternal ?? 0, 0, ',', '.') }}</p>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-700 mb-1">Staf</p>
                                                    <p>{{ $dist->staff_1_name }}: Rp {{ number_format($dist->staff_1_nominal, 0, ',', '.') }} ({{ number_format($dist->staff_1_percent, 2) }}%)</p>
                                                    <p>{{ $dist->staff_2_name }}: Rp {{ number_format($dist->staff_2_nominal, 0, ',', '.') }} ({{ number_format($dist->staff_2_percent, 2) }}%)</p>
                                                    <p>{{ $dist->staff_3_name }}: Rp {{ number_format($dist->staff_3_nominal, 0, ',', '.') }} ({{ number_format($dist->staff_3_percent, 2) }}%)</p>
                                                    <p>{{ $dist->staff_4_name }}: Rp {{ number_format($dist->staff_4_nominal, 0, ',', '.') }} ({{ number_format($dist->staff_4_percent, 2) }}%)</p>
                                                </div>
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-4 text-center text-gray-500">Belum ada data pembagian gaji.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
