<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Pesanan Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="ui-panel overflow-hidden">
                <div class="p-6 text-gray-900">

                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($orders as $order)
                        @php
                            $statusClass = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'processed' => 'bg-blue-100 text-blue-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ][$order->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <div class="bg-white border rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow relative overflow-hidden">
                            <!-- Status Banner -->
                            <div class="absolute top-0 right-0 px-3 py-1 text-xs font-bold uppercase rounded-bl-lg {{ $statusClass }}">
                                {{ ucfirst($order->status) }}
                            </div>

                            <div class="mb-2">
                                <h3 class="text-lg font-bold text-gray-900">Order #{{ $order->id }}</h3>
                                <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>

                            <ul class="mb-4 space-y-1">
                                @foreach($order->orderItems as $item)
                                <li class="text-sm flex justify-between items-center border-b border-gray-100 pb-1">
                                    <span class="text-gray-700 truncate w-2/3">{{ $item->product->name ?? 'Produk dihapus' }}</span>
                                    <span class="text-gray-900 font-medium">x{{ $item->quantity }}</span>
                                </li>
                                @endforeach
                            </ul>

                            @if($order->note && $order->note !== '-')
                            <div class="mb-3 bg-gray-50 p-2 rounded text-xs text-gray-600 italic">
                                "Note: {{ $order->note }}"
                            </div>
                            @endif

                            <div class="border-t pt-3 flex justify-between items-center">
                                <span class="text-gray-500 text-sm">Total Bayar:</span>
                                <span class="text-lg font-bold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>

                            <div class="mt-3 text-right">
                                <a href="{{ route('dapur.orders.invoice', $order->id) }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">Nota Pembelian</a>
                                @if($order->status === 'completed')
                                    <span class="mx-1 text-gray-300">|</span>
                                    <a href="{{ route('dapur.orders.sales-note', $order->id) }}" target="_blank" class="text-xs text-emerald-600 hover:text-emerald-800 font-semibold">Nota Penjualan Gudang</a>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center py-10 bg-gray-50 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pesanan</h3>
                            <p class="mt-1 text-sm text-gray-500">Mulai buat pesanan baru untuk stok dapur.</p>
                            <div class="mt-6">
                                <a href="{{ route('dapur.orders.create') }}" class="ui-btn-primary inline-flex items-center text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Buat Pesanan Baru
                                </a>
                            </div>
                        </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
