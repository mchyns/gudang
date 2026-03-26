<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nota Penjualan Supplier</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="ui-panel p-6">
                <p class="text-sm text-gray-500 mb-4">Daftar order pembelian gudang yang memuat produk Anda. Cetak nota tersedia setelah item Anda di-ACC.</p>

                <div class="ui-table-wrap">
                    <table class="min-w-full ui-table text-sm">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left">Order</th>
                                <th class="px-4 py-3 text-left">Tanggal</th>
                                <th class="px-4 py-3 text-left">Pembeli</th>
                                <th class="px-4 py-3 text-left">Ringkasan Barang</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-right">Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                @php
                                    $myItems = $order->orderItems->filter(fn($item) => $item->product && $item->product->supplier_id === Auth::id());
                                    $pendingMine = $myItems->filter(fn($item) => $item->supplier_approved_at === null);
                                    $approvedMine = $myItems->filter(fn($item) => $item->supplier_approved_at !== null);
                                @endphp
                                <tr>
                                    <td class="px-4 py-3">#{{ $order->id }}</td>
                                    <td class="px-4 py-3">{{ $order->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3">Gudang</td>
                                    <td class="px-4 py-3">
                                        <ul class="list-disc pl-4 text-xs text-gray-600 space-y-1">
                                            @foreach($myItems as $item)
                                                <li>{{ $item->product->name ?? '-' }} x{{ $item->quantity }} {{ $item->product->unit ?? 'pcs' }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($pendingMine->isNotEmpty())
                                            <form action="{{ route('supplier.orders.approve', $order) }}" method="POST" class="inline-flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-2 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 rounded text-[11px] font-semibold">ACC & Kirim</button>
                                            </form>
                                        @else
                                            <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-700 font-semibold">Di-ACC</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if($approvedMine->isNotEmpty())
                                            <a href="{{ route('supplier.orders.invoice', $order->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-semibold">Lihat Nota</a>
                                        @else
                                            <span class="text-gray-400 text-xs">Menunggu ACC</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">Belum ada nota untuk supplier ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
