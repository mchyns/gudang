<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Produk') }}
            </h2>
            @if(Auth::user()->role === 'supplier')
                <a href="{{ route('supplier.products.create') }}" class="ui-btn-primary text-sm font-medium">
                    + Tambah Produk
                </a>
            @endif
        </div>
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

                    <div class="ui-table-wrap">
                        <table class="min-w-[1200px] w-full divide-y divide-gray-200 ui-table">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ Auth::user()->role === 'supplier' ? 'Stok Supplier' : 'Stok Gudang' }}
                                    </th>
                                    @if(Auth::user()->hasRole(['admin', 'superadmin']))
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Awal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Masuk</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keluar</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Supplier</th>
                                    @if(Auth::user()->role !== 'supplier')
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual (Fix)</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($products as $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($product->image_url)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-12 w-12 rounded object-cover border border-gray-200" loading="lazy">
                                        @else
                                            <div class="h-12 w-12 rounded border border-dashed border-gray-300 bg-gray-50"></div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($product->description, 30) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $product->category->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 uppercase">
                                        {{ $product->unit ?? 'pcs' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                        {{ Auth::user()->role === 'supplier' ? $product->stock : $product->warehouse_stock }}
                                    </td>
                                    @if(Auth::user()->hasRole(['admin', 'superadmin']))
                                        @php
                                            $initial = $product->warehouse_initial_stock ?? 0;
                                            $keluar = (int) ($product->total_keluar_qty ?? 0);
                                            $masuk = max(($product->warehouse_stock + $keluar) - $initial, 0);
                                        @endphp
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $initial }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-emerald-700 font-semibold">{{ $masuk }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-700 font-semibold">{{ $keluar }}</td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Rp {{ number_format($product->supplier_price, 0, ',', '.') }}
                                    </td>
                                    @if(Auth::user()->role !== 'supplier')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">
                                            @if($product->price)
                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                            @else
                                                <span class="text-red-500 italic">Belum diset</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($product->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if(Auth::user()->role === 'supplier')
                                            <a href="{{ route('supplier.products.edit', $product->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                            <form action="{{ route('supplier.products.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus produk ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                            </form>
                                        @elseif(Auth::user()->role === 'admin')
                                            <a href="{{ route('admin.products.edit', $product->id) }}" class="inline-block whitespace-nowrap ui-btn-primary">Atur Harga</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
