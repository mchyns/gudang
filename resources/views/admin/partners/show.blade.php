<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Mitra: {{ $user->name }}</h2>
            <a href="{{ route('admin.partners.index') }}" class="ui-btn-ghost text-sm">Kembali ke Daftar Mitra</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="ui-panel p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs uppercase text-gray-500">Nama</p>
                        <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-500">Role</p>
                        <p class="font-semibold {{ $user->role === 'supplier' ? 'text-amber-700' : 'text-violet-700' }}">{{ ucfirst($user->role) }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-500">Nomor HP</p>
                        <p class="font-semibold text-gray-900">{{ $user->phone ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-500">Email</p>
                        <p class="font-semibold text-gray-900">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs uppercase text-gray-500">Alamat</p>
                    <p class="text-gray-800">{{ $user->address ?: '-' }}</p>
                </div>
            </div>

            @if($user->role === 'supplier')
                <div class="ui-panel p-5">
                    <h3 class="ui-title mb-3">Produk dari Supplier</h3>
                    <div class="ui-table-wrap">
                        <table class="min-w-full text-sm ui-table">
                            <thead>
                                <tr>
                                    <th class="text-left">Nama Produk</th>
                                    <th class="text-left">Kategori</th>
                                    <th class="text-left">Harga Supplier</th>
                                    <th class="text-left">Stok</th>
                                    <th class="text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($products as $product)
                                    <tr>
                                        <td class="font-medium text-gray-900">{{ $product->name }}</td>
                                        <td>{{ $product->category->name ?? '-' }}</td>
                                        <td>Rp {{ number_format($product->supplier_price, 0, ',', '.') }}</td>
                                        <td>{{ $product->stock }}</td>
                                        <td>{{ ucfirst($product->status) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-gray-500">Belum ada produk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $products->links() }}</div>
                </div>
            @endif

            @if($user->role === 'dapur')
                <div class="ui-panel p-5">
                    <h3 class="ui-title mb-3">Riwayat Order Dapur</h3>
                    <div class="ui-table-wrap">
                        <table class="min-w-full text-sm ui-table">
                            <thead>
                                <tr>
                                    <th class="text-left">Order</th>
                                    <th class="text-left">Tanggal</th>
                                    <th class="text-left">Total</th>
                                    <th class="text-left">Status</th>
                                    <th class="text-right">Nota</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($orders as $order)
                                    <tr>
                                        <td class="font-medium text-gray-900">#{{ $order->id }}</td>
                                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                        <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                        <td>{{ ucfirst($order->status) }}</td>
                                        <td class="text-right"><a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800">Lihat Nota</a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-gray-500">Belum ada order.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $orders->links() }}</div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
