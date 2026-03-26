<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Pesanan Masuk (Admin)') }}
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

                    <!-- Simple Status Legend -->
                    <div class="mb-4 flex flex-wrap gap-3 text-sm text-gray-600 ui-panel-soft px-4 py-3">
                        <span class="flex items-center"><span class="w-3 h-3 bg-yellow-100 border border-yellow-400 rounded-full mr-1"></span> Pending (Menunggu)</span>
                        <span class="flex items-center"><span class="w-3 h-3 bg-blue-100 border border-blue-400 rounded-full mr-1"></span> Processed (Diproses)</span>
                        <span class="flex items-center"><span class="w-3 h-3 bg-green-100 border border-green-400 rounded-full mr-1"></span> Completed (Selesai)</span>
                        <span class="flex items-center"><span class="w-3 h-3 bg-red-100 border border-red-400 rounded-full mr-1"></span> Cancelled (Batal)</span>
                    </div>

                    @if(Auth::user()->role === 'admin')
                        <div class="mb-4 flex flex-wrap justify-between gap-3">
                            <div class="inline-flex rounded-lg border border-gray-200 bg-gray-50 p-1">
                                <a
                                    href="{{ route('admin.orders.index', ['order_type' => 'dapur_sale']) }}"
                                    class="px-3 py-1.5 text-sm rounded-md {{ $activeOrderType === 'dapur_sale' ? 'bg-white text-indigo-700 shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-800' }}"
                                >
                                    Kelola Pesanan Dapur
                                </a>
                                <a
                                    href="{{ route('admin.orders.index', ['order_type' => 'supplier_purchase']) }}"
                                    class="px-3 py-1.5 text-sm rounded-md {{ $activeOrderType === 'supplier_purchase' ? 'bg-white text-indigo-700 shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-800' }}"
                                >
                                    Kelola Pembelian Supplier
                                </a>
                            </div>

                            @if($activeOrderType === 'supplier_purchase')
                                <a href="{{ route('admin.orders.supplier-purchase.create') }}" class="ui-btn-primary text-sm">Belanja Supplier</a>
                            @else
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.orders.dapur-purchase-note', ['period' => 'daily', 'date' => now()->toDateString()]) }}" target="_blank" class="ui-btn-ghost text-sm">Nota Harian</a>
                                    <a href="{{ route('admin.orders.dapur-purchase-note', ['period' => 'weekly', 'date' => now()->toDateString()]) }}" target="_blank" class="ui-btn-ghost text-sm">Nota Mingguan</a>
                                    <a href="{{ route('admin.orders.dapur-purchase-note', ['period' => 'monthly', 'date' => now()->toDateString()]) }}" target="_blank" class="ui-btn-ghost text-sm">Nota Bulanan</a>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="ui-table-wrap">
                        <table class="min-w-full divide-y divide-gray-200 ui-table">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemesan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengiriman</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ubah Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($orders as $order)
                                <tr class="{{ $order->status === 'cancelled' ? 'bg-gray-50 opacity-60' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ $order->id }}
                                        <div class="text-xs text-gray-400">{{ $order->created_at->format('d M H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($order->order_type === 'supplier_purchase')
                                            <span class="px-2 py-1 rounded-full text-xs bg-indigo-100 text-indigo-700">Pembelian Supplier</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-xs bg-emerald-100 text-emerald-700">Penjualan Dapur</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $order->user->name ?? 'User Hapus' }} <br>
                                        @if(($order->user->role ?? null) === 'dapur')
                                            <span class="block text-xs text-gray-600">HP: {{ $order->user->phone ?: '-' }}</span>
                                            <span class="block text-xs text-gray-600">Alamat: {{ $order->user->address ?: '-' }}</span>
                                        @endif
                                        <span class="text-xs italic">{{ $order->note }}</span>
                                        @if($order->admin_note)
                                            <span class="block text-xs text-indigo-700 mt-1">Catatan Admin: {{ $order->admin_note }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <ul class="list-disc pl-4 text-xs">
                                            @foreach($order->orderItems as $item)
                                                <li>
                                                    {{ $item->product->name ?? 'Produk Dihapus' }} 
                                                    <span class="font-bold">x{{ $item->quantity }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $badges = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'processed' => 'bg-blue-100 text-blue-800',
                                                'completed' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badges[$order->status] ?? 'bg-gray-100' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $shippingBadges = [
                                                'pending' => 'bg-gray-100 text-gray-700',
                                                'prepared' => 'bg-amber-100 text-amber-700',
                                                'shipped' => 'bg-blue-100 text-blue-700',
                                                'delivered' => 'bg-emerald-100 text-emerald-700',
                                                'cancelled' => 'bg-rose-100 text-rose-700',
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $shippingBadges[$order->shipping_status ?? 'pending'] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ ucfirst($order->shipping_status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if(Auth::user()->role === 'admin' && $order->order_type === 'dapur_sale')
                                            <a href="{{ route('admin.orders.operational.edit', $order->id) }}" class="text-amber-600 hover:text-amber-800 text-xs mb-1 inline-block">Detail Proses</a><br>
                                        @endif
                                        <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-xs mb-1 inline-block">Cetak Nota</a>

                                        @if($order->status !== 'cancelled' && $order->status !== 'completed')
                                        <div class="flex flex-col space-y-1 items-end">
                                            @if($order->status === 'pending' && $order->order_type === 'dapur_sale')
                                                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <button name="status" value="processed" class="text-blue-600 hover:text-blue-900 text-xs bg-blue-50 px-2 py-1 rounded border border-blue-200">
                                                        Proses &rarr;
                                                    </button>
                                                </form>
                                            @elseif($order->status === 'processed' && $order->order_type === 'dapur_sale')
                                                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <button name="status" value="completed" class="text-green-600 hover:text-green-900 text-xs bg-green-50 px-2 py-1 rounded border border-green-200">
                                                        Selesaikan &check;
                                                    </button>
                                                </form>
                                            @elseif($order->status === 'processed' && $order->order_type === 'supplier_purchase')
                                                <form action="{{ route('admin.orders.receive', $order->id) }}" method="POST" onsubmit="return confirm('Konfirmasi barang sudah diterima di gudang?');">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="text-emerald-700 hover:text-emerald-900 text-xs bg-emerald-50 px-2 py-1 rounded border border-emerald-200">
                                                        Terima Barang di Gudang
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" onsubmit="return confirm('Yakin batalkan pesanan ini? Stok akan dikembalikan.');">
                                                @csrf @method('PATCH')
                                                <button name="status" value="cancelled" class="text-red-600 hover:text-red-900 text-xs">
                                                    Batalkan
                                                </button>
                                            </form>
                                        </div>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada pesanan masuk.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination Links if available --}}
                    {{-- {{ $orders->links() }} --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
