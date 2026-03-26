<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Rincian Operasional Order #{{ $order->id }}</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="ui-panel p-6 space-y-5">
                <div class="ui-panel-soft p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Detail Pengiriman Dapur</h3>
                    <p class="text-sm text-gray-600"><span class="font-medium">Mitra:</span> {{ $order->user->name ?? '-' }}</p>
                    <p class="text-sm text-gray-600"><span class="font-medium">No. Telepon:</span> {{ $order->user->phone ?: '-' }}</p>
                    <p class="text-sm text-gray-600"><span class="font-medium">Alamat:</span> {{ $order->user->address ?: '-' }}</p>
                    <p class="text-sm text-gray-600 mt-2"><span class="font-medium">Catatan Dapur:</span> {{ $order->note ?: '-' }}</p>
                </div>

                <form method="POST" action="{{ route('admin.orders.operational.update', $order) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Status Pengiriman</label>
                            <select name="shipping_status" class="w-full border-gray-300 rounded-md" required>
                                <option value="pending" @selected(old('shipping_status', $order->shipping_status) === 'pending')>Pending</option>
                                <option value="prepared" @selected(old('shipping_status', $order->shipping_status) === 'prepared')>Prepared</option>
                                <option value="shipped" @selected(old('shipping_status', $order->shipping_status) === 'shipped')>Shipped</option>
                                <option value="delivered" @selected(old('shipping_status', $order->shipping_status) === 'delivered')>Delivered</option>
                                <option value="cancelled" @selected(old('shipping_status', $order->shipping_status) === 'cancelled')>Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Catatan Admin (Opsional)</label>
                            <input type="text" name="admin_note" value="{{ old('admin_note', $order->admin_note) }}" class="w-full border-gray-300 rounded-md" placeholder="Catatan proses admin untuk order ini">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Tanggal Drop</label>
                            <input type="date" name="drop_date" value="{{ old('drop_date', optional($order->drop_date)->format('Y-m-d')) }}" class="w-full border-gray-300 rounded-md">
                            <p class="text-xs text-gray-500 mt-1">Tanggal ini akan dipakai otomatis di header nota pembelian dapur.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">1. Bensin</label>
                            <input type="text" inputmode="numeric" name="operational_bensin" value="{{ old('operational_bensin', $order->operational_bensin) }}" class="w-full border-gray-300 rounded-md js-nominal" placeholder="Boleh kosong">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">2. Kuli</label>
                            <input type="text" inputmode="numeric" name="operational_kuli" value="{{ old('operational_kuli', $order->operational_kuli) }}" class="w-full border-gray-300 rounded-md js-nominal" placeholder="Boleh kosong">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">3. Makan Minum</label>
                            <input type="text" inputmode="numeric" name="operational_makan_minum" value="{{ old('operational_makan_minum', $order->operational_makan_minum) }}" class="w-full border-gray-300 rounded-md js-nominal" placeholder="Boleh kosong">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">4. Listrik</label>
                            <input type="text" inputmode="numeric" name="operational_listrik" value="{{ old('operational_listrik', $order->operational_listrik) }}" class="w-full border-gray-300 rounded-md js-nominal" placeholder="Boleh kosong">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-700 mb-1">5. Wifi</label>
                            <input type="text" inputmode="numeric" name="operational_wifi" value="{{ old('operational_wifi', $order->operational_wifi) }}" class="w-full border-gray-300 rounded-md js-nominal" placeholder="Boleh kosong">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm text-gray-700">Operasional Tambahan</label>
                            <button type="button" id="addOperationalRow" class="ui-btn-ghost text-xs">+ Tambah Baris</button>
                        </div>

                        <div id="operationalExtraRows" class="space-y-2">
                            @php
                                $oldLabels = old('extra_labels', []);
                                $oldAmounts = old('extra_amounts', []);
                                $savedExtras = is_array($order->operational_extras) ? $order->operational_extras : [];
                                $rows = count($oldLabels) > 0
                                    ? collect($oldLabels)->map(function ($label, $idx) use ($oldAmounts) {
                                        return ['label' => $label, 'amount' => $oldAmounts[$idx] ?? ''];
                                    })->values()->all()
                                    : $savedExtras;
                            @endphp

                            @forelse($rows as $row)
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center operational-extra-row">
                                    <input type="text" name="extra_labels[]" value="{{ $row['label'] ?? '' }}" class="md:col-span-6 border-gray-300 rounded-md" placeholder="Nama operasional (contoh: Parkir)">
                                    <input type="text" inputmode="numeric" name="extra_amounts[]" value="{{ isset($row['amount']) ? number_format((float) $row['amount'], 0, ',', '.') : '' }}" class="md:col-span-5 border-gray-300 rounded-md js-nominal" placeholder="Nominal">
                                    <button type="button" class="md:col-span-1 text-xs text-rose-600 remove-operational-row">Hapus</button>
                                </div>
                            @empty
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center operational-extra-row">
                                    <input type="text" name="extra_labels[]" class="md:col-span-6 border-gray-300 rounded-md" placeholder="Nama operasional (contoh: Parkir)">
                                    <input type="text" inputmode="numeric" name="extra_amounts[]" class="md:col-span-5 border-gray-300 rounded-md js-nominal" placeholder="Nominal">
                                    <button type="button" class="md:col-span-1 text-xs text-rose-600 remove-operational-row">Hapus</button>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-2">
                        <a href="{{ route('admin.orders.index') }}" class="ui-btn-ghost">Kembali</a>
                        <button class="ui-btn-primary">Simpan Proses Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('click', function (event) {
            if (event.target.matches('.remove-operational-row')) {
                const row = event.target.closest('.operational-extra-row');
                if (row) {
                    row.remove();
                }
            }
        });

        document.getElementById('addOperationalRow')?.addEventListener('click', function () {
            const wrapper = document.getElementById('operationalExtraRows');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 md:grid-cols-12 gap-2 items-center operational-extra-row';
            row.innerHTML = `
                <input type="text" name="extra_labels[]" class="md:col-span-6 border-gray-300 rounded-md" placeholder="Nama operasional (contoh: Parkir)">
                <input type="text" inputmode="numeric" name="extra_amounts[]" class="md:col-span-5 border-gray-300 rounded-md js-nominal" placeholder="Nominal">
                <button type="button" class="md:col-span-1 text-xs text-rose-600 remove-operational-row">Hapus</button>
            `;
            wrapper.appendChild(row);
        });
    </script>
</x-app-layout>
