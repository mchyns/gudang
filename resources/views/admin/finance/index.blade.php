<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laba, Untung/Rugi, dan Pembagian Gaji</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="ui-panel p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-800">Ringkasan Keuntungan / Kerugian ({{ $periodLabel }})</h3>
                        <p class="text-sm text-gray-500">Periode {{ $start->format('d M Y') }} - {{ $end->format('d M Y') }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.finance.index', ['period' => 'daily']) }}" class="px-3 py-2 rounded text-sm {{ $periodType === 'daily' ? 'ui-btn-primary' : 'ui-btn-ghost' }}">Harian</a>
                        <a href="{{ route('admin.finance.index', ['period' => 'weekly']) }}" class="px-3 py-2 rounded text-sm {{ $periodType === 'weekly' ? 'ui-btn-primary' : 'ui-btn-ghost' }}">Mingguan</a>
                        <a href="{{ route('admin.finance.index', ['period' => 'monthly']) }}" class="px-3 py-2 rounded text-sm {{ $periodType === 'monthly' ? 'ui-btn-primary' : 'ui-btn-ghost' }}">Bulanan</a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-5">
                    <div class="ui-kpi bg-amber-50 border-amber-100">
                        <p class="text-xs uppercase text-amber-700">Harga Beli Supplier</p>
                        <p class="text-lg font-bold text-amber-800">Rp {{ number_format($hargaBeliSupplier, 0, ',', '.') }}</p>
                    </div>
                    <div class="ui-kpi bg-sky-50 border-sky-100">
                        <p class="text-xs uppercase text-sky-700">Total Operasional</p>
                        <p class="text-lg font-bold text-sky-800">Rp {{ number_format($operasionalTotal, 0, ',', '.') }}</p>
                    </div>
                    <div class="ui-kpi bg-emerald-50 border-emerald-100">
                        <p class="text-xs uppercase text-emerald-700">Harga Beli Dapur</p>
                        <p class="text-lg font-bold text-emerald-800">Rp {{ number_format($hargaBeliDapur, 0, ',', '.') }}</p>
                    </div>
                    <div class="ui-kpi {{ $labaKotor >= 0 ? 'bg-indigo-50 border-indigo-100' : 'bg-rose-50 border-rose-100' }}">
                        <p class="text-xs uppercase {{ $labaKotor >= 0 ? 'text-indigo-700' : 'text-rose-700' }}">{{ $labaKotor >= 0 ? 'Keuntungan (Laba Kotor)' : 'Kerugian (Laba Kotor)' }}</p>
                        <p class="text-lg font-bold {{ $labaKotor >= 0 ? 'text-indigo-800' : 'text-rose-800' }}">Rp {{ number_format($labaKotor, 0, ',', '.') }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-3">Rumus: H.B.S + Operasional - Harga Beli Dapur.</p>
            </div>

            <div class="ui-panel p-5">
                <h3 class="font-semibold text-gray-800 mb-1">Kalkulator Pembagian Gaji</h3>
                <p class="text-sm text-gray-500 mb-4">Internal diisi 4 orang (Kepala Dapur, Asisten Lapangan, Ahli Gizi, Akuntan), eksternal 2 orang (Kodim, Koramil). Setelah itu sistem hitung sisa pendapatan, bagian Pak Ued, dan pembagian staf.</p>

                @if($errors->any())
                    <div class="mb-4 bg-rose-100 border border-rose-200 text-rose-700 px-4 py-3 rounded">
                        <ul class="list-disc pl-5 text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="finance-distribution-form" action="{{ route('admin.finance.distribution.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Tipe Periode</label>
                            <select name="period_type" class="w-full rounded border-gray-300" required>
                                <option value="daily" @selected(old('period_type') === 'daily')>Harian</option>
                                <option value="weekly" @selected(old('period_type') === 'weekly')>Mingguan</option>
                                <option value="monthly" @selected(old('period_type', 'monthly') === 'monthly')>Bulanan</option>
                                <option value="manual" @selected(old('period_type') === 'manual')>Manual</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Label Periode (Opsional)</label>
                            <input type="text" name="period_label" value="{{ old('period_label', $periodLabel) }}" class="w-full rounded border-gray-300" placeholder="Contoh: Maret 2026">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Tanggal Mulai (Opsional)</label>
                            <input type="date" name="period_start" value="{{ old('period_start') }}" class="w-full rounded border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Tanggal Selesai (Opsional)</label>
                            <input type="date" name="period_end" value="{{ old('period_end') }}" class="w-full rounded border-gray-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Pendapatan Untuk Dibagi</label>
                            <input type="text" inputmode="numeric" name="pendapatan_total" value="{{ old('pendapatan_total', max($labaKotor, 0)) }}" class="w-full rounded border-gray-300 js-nominal" required>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Internal - Kepala Dapur</label>
                            <input type="text" inputmode="numeric" name="gaji_internal_kepala_dapur" value="{{ old('gaji_internal_kepala_dapur', 0) }}" class="w-full rounded border-gray-300 js-nominal" required>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Internal - Asisten Lapangan</label>
                            <input type="text" inputmode="numeric" name="gaji_internal_asisten_lapangan" value="{{ old('gaji_internal_asisten_lapangan', 0) }}" class="w-full rounded border-gray-300 js-nominal" required>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Internal - Ahli Gizi</label>
                            <input type="text" inputmode="numeric" name="gaji_internal_ahli_gizi" value="{{ old('gaji_internal_ahli_gizi', 0) }}" class="w-full rounded border-gray-300 js-nominal" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Internal - Akuntan</label>
                            <input type="text" inputmode="numeric" name="gaji_internal_akuntan" value="{{ old('gaji_internal_akuntan', 0) }}" class="w-full rounded border-gray-300 js-nominal" required>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Gaji Eksternal Kodim</label>
                            <input type="text" inputmode="numeric" name="gaji_eksternal_kodim" value="{{ old('gaji_eksternal_kodim', 0) }}" class="w-full rounded border-gray-300 js-nominal" required>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Gaji Eksternal Koramil</label>
                            <input type="text" inputmode="numeric" name="gaji_eksternal_koramil" value="{{ old('gaji_eksternal_koramil', 0) }}" class="w-full rounded border-gray-300 js-nominal" required>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">% Pak Ued dari Sisa</label>
                            <input type="number" step="0.01" min="0" max="100" name="kepala_dapur_percent" value="{{ old('kepala_dapur_percent', 50) }}" class="w-full rounded border-gray-300" required>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h4 class="font-semibold text-gray-800 mb-2">Pembagian Sisa ke Staf</h4>
                        <p class="text-xs text-gray-500 mb-3">Isi persen untuk Pak Suci, Bu Suci, Pak Juki, dan Pak Kholik. Total persen staf harus 100%.</p>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Pak Suci</label>
                                <input type="number" step="0.01" min="0" max="100" name="staff_1_percent" value="{{ old('staff_1_percent', 45) }}" class="w-full rounded border-gray-300" placeholder="%">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Bu Suci</label>
                                <input type="number" step="0.01" min="0" max="100" name="staff_2_percent" value="{{ old('staff_2_percent', 5) }}" class="w-full rounded border-gray-300" placeholder="%">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Pak Juki</label>
                                <input type="number" step="0.01" min="0" max="100" name="staff_3_percent" value="{{ old('staff_3_percent', 30) }}" class="w-full rounded border-gray-300" placeholder="%">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Pak Kholik (Staf 4)</label>
                                <input type="number" step="0.01" min="0" max="100" name="staff_4_percent" value="{{ old('staff_4_percent', 20) }}" class="w-full rounded border-gray-300" placeholder="%">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Catatan (Opsional)</label>
                        <textarea name="notes" rows="2" class="w-full rounded border-gray-300" placeholder="Catatan tambahan pembagian gaji">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="ui-btn-primary">Hitung & Simpan Pembagian</button>
                </form>
            </div>

            <div class="ui-panel p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Riwayat Pembagian Gaji</h3>
                <div class="ui-table-wrap">
                    <table class="min-w-[1500px] w-full text-sm ui-table">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left whitespace-nowrap">Waktu</th>
                                <th class="px-3 py-2 text-left whitespace-nowrap">Periode</th>
                                <th class="px-3 py-2 text-left whitespace-nowrap">Pendapatan</th>
                                <th class="px-3 py-2 text-left whitespace-nowrap">Internal (4 Orang)</th>
                                <th class="px-3 py-2 text-left whitespace-nowrap">Eksternal</th>
                                <th class="px-3 py-2 text-left whitespace-nowrap">Total Gaji</th>
                                <th class="px-3 py-2 text-left whitespace-nowrap">Sisa</th>
                                <th class="px-3 py-2 text-left whitespace-nowrap">Pak Ued</th>
                                <th class="px-3 py-2 text-left whitespace-nowrap">Staf 1</th>
                                <th class="px-3 py-2 text-left whitespace-nowrap">Staf 2</th>
                                <th class="px-3 py-2 text-left whitespace-nowrap">Staf 3</th>
                                <th class="px-3 py-2 text-left whitespace-nowrap">Staf 4</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($distributions as $dist)
                                <tr class="align-top">
                                    <td class="px-3 py-3 whitespace-nowrap">{{ $dist->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap">{{ $dist->period_label ?? strtoupper($dist->period_type) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap font-medium">Rp {{ number_format($dist->pendapatan_total, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 min-w-[260px]">
                                        <div class="space-y-1 text-xs">
                                            <div class="flex justify-between gap-4"><span class="text-gray-500">Kepala Dapur</span><span class="font-medium whitespace-nowrap">Rp {{ number_format($dist->gaji_internal_kepala_dapur ?? 0, 0, ',', '.') }}</span></div>
                                            <div class="flex justify-between gap-4"><span class="text-gray-500">Asisten Lapangan</span><span class="font-medium whitespace-nowrap">Rp {{ number_format($dist->gaji_internal_asisten_lapangan ?? 0, 0, ',', '.') }}</span></div>
                                            <div class="flex justify-between gap-4"><span class="text-gray-500">Ahli Gizi</span><span class="font-medium whitespace-nowrap">Rp {{ number_format($dist->gaji_internal_ahli_gizi ?? 0, 0, ',', '.') }}</span></div>
                                            <div class="flex justify-between gap-4"><span class="text-gray-500">Akuntan</span><span class="font-medium whitespace-nowrap">Rp {{ number_format($dist->gaji_internal_akuntan ?? 0, 0, ',', '.') }}</span></div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 min-w-[170px]">
                                        <div class="space-y-1 text-xs">
                                            <div class="flex justify-between gap-3"><span class="text-gray-500">Kodim</span><span class="font-medium whitespace-nowrap">Rp {{ number_format($dist->gaji_eksternal_kodim ?? 0, 0, ',', '.') }}</span></div>
                                            <div class="flex justify-between gap-3"><span class="text-gray-500">Koramil</span><span class="font-medium whitespace-nowrap">Rp {{ number_format($dist->gaji_eksternal_koramil ?? 0, 0, ',', '.') }}</span></div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">Rp {{ number_format($dist->total_gaji, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap {{ $dist->sisa_setelah_gaji >= 0 ? 'text-emerald-700' : 'text-rose-700' }} font-semibold">Rp {{ number_format($dist->sisa_setelah_gaji, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap">Rp {{ number_format($dist->kepala_dapur_nominal, 0, ',', '.') }} <span class="text-gray-500">({{ number_format($dist->kepala_dapur_percent, 2) }}%)</span></td>
                                    <td class="px-3 py-3 whitespace-nowrap">{{ $dist->staff_1_name }}: Rp {{ number_format($dist->staff_1_nominal, 0, ',', '.') }} <span class="text-gray-500">({{ number_format($dist->staff_1_percent, 2) }}%)</span></td>
                                    <td class="px-3 py-3 whitespace-nowrap">{{ $dist->staff_2_name }}: Rp {{ number_format($dist->staff_2_nominal, 0, ',', '.') }} <span class="text-gray-500">({{ number_format($dist->staff_2_percent, 2) }}%)</span></td>
                                    <td class="px-3 py-3 whitespace-nowrap">{{ $dist->staff_3_name }}: Rp {{ number_format($dist->staff_3_nominal, 0, ',', '.') }} <span class="text-gray-500">({{ number_format($dist->staff_3_percent, 2) }}%)</span></td>
                                    <td class="px-3 py-3 whitespace-nowrap">{{ $dist->staff_4_name }}: Rp {{ number_format($dist->staff_4_nominal, 0, ',', '.') }} <span class="text-gray-500">({{ number_format($dist->staff_4_percent, 2) }}%)</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-3 py-4 text-center text-gray-500">Belum ada riwayat pembagian gaji.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $distributions->links() }}</div>
            </div>
        </div>
    </div>

</x-app-layout>
