<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Review Nota Penjualan Gudang #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc ml-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                    <div><span class="text-gray-500">Mitra Dapur:</span> <span class="font-semibold text-gray-900">{{ $order->user->name ?? '-' }}</span></div>
                    <div><span class="text-gray-500">Tanggal Order:</span> <span class="font-semibold text-gray-900">{{ $order->created_at->format('d M Y H:i') }}</span></div>
                    <div>
                        <span class="text-gray-500">Status Nota:</span>
                        @if($order->dapur_sales_note_locked_at)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">FIX / TERKUNCI</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">DRAFT</span>
                        @endif
                    </div>
                </div>
                @if($order->dapur_sales_note_locked_at)
                    <p class="mt-3 text-xs text-gray-500">Dikunci pada {{ $order->dapur_sales_note_locked_at->format('d M Y H:i') }}. Data tidak bisa diubah lagi.</p>
                @endif
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                @php $isLocked = !is_null($order->dapur_sales_note_locked_at); @endphp
                <form action="{{ route('dapur.orders.sales-note.update', $order->id) }}" method="POST" class="p-5 space-y-5">
                    @csrf
                    @method('PATCH')

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wide">
                                <tr>
                                    <th class="text-left px-3 py-2">Barang</th>
                                    <th class="text-right px-3 py-2">Qty Order</th>
                                    <th class="text-right px-3 py-2">Qty Final</th>
                                    <th class="text-left px-3 py-2">Catatan Dapur</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($order->orderItems as $idx => $item)
                                    <tr>
                                        <td class="px-3 py-3 align-top">
                                            <div class="font-semibold text-gray-900">{{ $item->product->name ?? 'Produk Dihapus' }}</div>
                                            <div class="text-xs text-gray-500">Satuan: {{ strtoupper($item->product->unit ?? 'PCS') }}</div>
                                        </td>
                                        <td class="px-3 py-3 align-top text-right font-medium text-gray-700">{{ $item->quantity }}</td>
                                        <td class="px-3 py-3 align-top text-right">
                                            <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $item->id }}">
                                            <input
                                                type="number"
                                                min="0"
                                                max="{{ $item->quantity }}"
                                                name="items[{{ $idx }}][final_quantity]"
                                                value="{{ old("items.$idx.final_quantity", $item->dapur_final_quantity ?? $item->quantity) }}"
                                                class="w-24 rounded-md border-gray-300 text-right text-sm"
                                                @if($isLocked) readonly @endif
                                            >
                                        </td>
                                        <td class="px-3 py-3 align-top">
                                            <textarea
                                                name="items[{{ $idx }}][item_note]"
                                                rows="2"
                                                class="w-full rounded-md border-gray-300 text-sm"
                                                placeholder="Contoh: 1 pcs rusak saat diterima"
                                                @if($isLocked) readonly @endif
                                            >{{ old("items.$idx.item_note", $item->dapur_item_note) }}</textarea>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Penyesuaian Umum Dapur</label>
                        <textarea
                            name="dapur_adjustment_note"
                            rows="3"
                            class="w-full rounded-md border-gray-300 text-sm"
                            placeholder="Contoh: Terjadi susut karena proses sortir"
                            @if($isLocked) readonly @endif
                        >{{ old('dapur_adjustment_note', $order->dapur_adjustment_note) }}</textarea>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-gray-100">
                        @if($isLocked)
                            <button type="submit" disabled class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-500 cursor-not-allowed border border-gray-300">
                                Simpan Draft Nota
                            </button>
                        @else
                            <button type="submit" class="inline-flex items-center rounded-md bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                                Simpan Draft Nota
                            </button>
                        @endif

                        @if($isLocked)
                            <a href="{{ route('dapur.orders.sales-note.print', $order->id) }}" target="_blank" class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                Cetak Nota Final
                            </a>
                        @else
                            <button type="button" disabled class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-500 cursor-not-allowed border border-gray-300">
                                Cetak Nota Final
                            </button>
                        @endif

                        <a href="{{ route('dapur.orders.my_orders') }}" class="inline-flex items-center rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                            Kembali ke Riwayat
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white border border-amber-200 rounded-xl shadow-sm p-5">
                <p class="text-sm text-amber-700 mb-3">Setelah dikunci, nota tidak bisa diubah lagi dan siap untuk dicetak.</p>
                <form action="{{ route('dapur.orders.sales-note.finalize', $order->id) }}" method="POST" onsubmit="return confirm('Yakin fix dan kunci nota ini? Data tidak bisa diubah lagi.');">
                    @csrf
                    @method('PATCH')
                    @if($isLocked)
                        <button type="submit" disabled class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-500 cursor-not-allowed border border-gray-300">
                            Fix dan Kunci Nota
                        </button>
                    @else
                        <button type="submit" class="inline-flex items-center rounded-md bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600">
                            Fix dan Kunci Nota
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
