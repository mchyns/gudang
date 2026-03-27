<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Proses Order #{{ $order->id }}</h2>
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

                <div class="ui-panel-soft p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Review Nota Dapur</h3>
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Status Nota:</span>
                        @if($order->dapur_sales_note_locked_at)
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">FIX / TERKUNCI</span>
                        @else
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">DRAFT</span>
                        @endif
                    </p>
                    <p class="text-sm text-gray-600 mt-2"><span class="font-medium">Catatan Penyesuaian Dapur:</span> {{ $order->dapur_adjustment_note ?: '-' }}</p>

                    <div class="mt-3 overflow-x-auto">
                        <table class="min-w-full text-xs border border-gray-200 rounded-md overflow-hidden">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-2 py-2 text-left">Barang</th>
                                    <th class="px-2 py-2 text-right">Qty Order</th>
                                    <th class="px-2 py-2 text-right">Qty Final</th>
                                    <th class="px-2 py-2 text-left">Catatan Item Dapur</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td class="px-2 py-2">{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                                        <td class="px-2 py-2 text-right">{{ $item->quantity }}</td>
                                        <td class="px-2 py-2 text-right">{{ $item->dapur_final_quantity ?? '-' }}</td>
                                        <td class="px-2 py-2">{{ $item->dapur_item_note ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @php
                    $shortageItems = $order->orderItems->map(function ($item) {
                        $ordered = (int) $item->quantity;
                        $final = (int) ($item->dapur_final_quantity ?? $ordered);
                        return [
                            'id' => $item->id,
                            'name' => $item->product->name ?? 'Produk Dihapus',
                            'ordered' => $ordered,
                            'final' => $final,
                            'shortage' => max($ordered - $final, 0),
                        ];
                    })->filter(fn($row) => $row['shortage'] > 0)->values();
                @endphp

                @if($shortageItems->isNotEmpty() && !$order->dapur_sales_note_locked_at)
                    <div class="ui-panel-soft p-4">
                        <h3 class="font-semibold text-gray-800 mb-2">Kirim Kekurangan Barang ke Dapur</h3>
                        <p class="text-xs text-gray-500 mb-3">Gunakan order yang sama. Jika stok gudang kurang, buat PO supplier dari order ini lalu kirim ulang setelah stok masuk.</p>

                        <form method="POST" action="{{ route('admin.orders.send-replacement', $order) }}" class="space-y-3">
                            @csrf
                            @method('PATCH')

                            <div class="overflow-x-auto">
                                <table class="min-w-full text-xs border border-gray-200 rounded-md overflow-hidden">
                                    <thead class="bg-gray-50 text-gray-600">
                                        <tr>
                                            <th class="px-2 py-2 text-left">Barang</th>
                                            <th class="px-2 py-2 text-right">Kurang</th>
                                            <th class="px-2 py-2 text-right">Kirim Sekarang</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($shortageItems as $idx => $row)
                                            <tr>
                                                <td class="px-2 py-2">{{ $row['name'] }}</td>
                                                <td class="px-2 py-2 text-right font-semibold text-amber-700">{{ $row['shortage'] }}</td>
                                                <td class="px-2 py-2 text-right">
                                                    <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $row['id'] }}">
                                                    <input
                                                        type="number"
                                                        name="items[{{ $idx }}][send_quantity]"
                                                        min="0"
                                                        max="{{ $row['shortage'] }}"
                                                        value="{{ old("items.$idx.send_quantity", $row['shortage']) }}"
                                                        class="w-24 rounded-md border-gray-300 text-right text-xs"
                                                    >
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Catatan Admin untuk Nota Penjualan (opsional)</label>
                                <input type="text" name="admin_note" value="{{ old('admin_note', $order->admin_note) }}" class="w-full border-gray-300 rounded-md text-sm" placeholder="Contoh: Transfer ke BRI xxxx / Kirim ulang via armada sore ini">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="ui-btn-primary text-sm">Proses Kirim Kekurangan</button>
                            </div>
                        </form>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.orders.operational.update', $order) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                            <label class="block text-sm text-gray-700 mb-1">Tanggal Drop</label>
                            <input type="date" name="drop_date" value="{{ old('drop_date', optional($order->drop_date)->format('Y-m-d')) }}" class="w-full border-gray-300 rounded-md">
                            <p class="text-xs text-gray-500 mt-1">Dipakai untuk header nota pembelian dapur.</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Catatan Admin untuk Nota Penjualan</label>
                            <input type="text" name="admin_note" value="{{ old('admin_note', $order->admin_note) }}" class="w-full border-gray-300 rounded-md" placeholder="Contoh: Transfer ke BRI xxxx / Instruksi pengiriman">
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
</x-app-layout>
