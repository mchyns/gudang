
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(Auth::user()->role === 'superadmin')
                {{ __('Dashboard Superadmin') }}
            @elseif(Auth::user()->role === 'admin')
                {{ __('Dashboard Admin Gudang') }}
            @elseif(Auth::user()->role === 'supplier')
                {{ __('Dashboard Supplier') }}
            @elseif(Auth::user()->role === 'dapur')
                {{ __('Dashboard Mitra Dapur') }}
            @else
                {{ __('Dashboard') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- SUPERADMIN VIEW -->
            @if(Auth::user()->role === 'superadmin')
                <div class="rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 via-white to-emerald-50 p-5">
                    <h3 class="text-lg font-bold text-emerald-900">Pusat Pantauan Superadmin</h3>
                    <p class="text-sm text-emerald-700 mt-1">Seluruh ringkasan bisnis dan transparansi lintas role tersedia di sini.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <div class="text-sm font-medium text-gray-500">Total Mitra Dapur</div>
                        <div class="mt-2 text-3xl font-bold text-gray-800">{{ \App\Models\User::where('role', 'dapur')->count() }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <div class="text-sm font-medium text-gray-500">Total Supplier</div>
                        <div class="mt-2 text-3xl font-bold text-gray-800">{{ \App\Models\User::where('role', 'supplier')->count() }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <div class="text-sm font-medium text-gray-500">Total Produk</div>
                        <div class="mt-2 text-3xl font-bold text-gray-800">{{ \App\Models\Product::count() }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <div class="text-sm font-medium text-gray-500">Total Order</div>
                        <div class="mt-2 text-3xl font-bold text-gray-800">{{ \App\Models\Order::count() }}</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4">Laporan Aktivitas Gudang</h3>
                        <p class="text-gray-600 mb-4">Pantau laba, mitra dapur aktif, supplier, dan perbandingan harga supplier vs harga fix admin.</p>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('superadmin.insights.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">Buka Insight Superadmin</a>
                            <a href="{{ route('admin.activity-logs.index') }}" class="px-4 py-2 bg-white border border-indigo-200 text-indigo-700 rounded text-sm hover:bg-indigo-50">Lihat Log Aktivitas</a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- ADMIN GUDANG VIEW -->
            @if(Auth::user()->role === 'admin')
                <div class="rounded-2xl border border-blue-200 bg-gradient-to-r from-blue-50 via-white to-blue-50 p-5">
                    <h3 class="text-lg font-bold text-blue-900">Panel Kerja Admin Gudang</h3>
                    <p class="text-sm text-blue-700 mt-1">Fokus pada pesanan masuk, stok, harga, dan operasional harian gudang.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 rounded-lg shadow-sm border border-blue-100 bg-blue-50">
                        <h3 class="font-bold text-blue-800 mb-2">Order Masuk (Dapur)</h3>
                        <p class="text-sm text-blue-600 mb-4">Kelola pesanan dari mitra dapur yang perlu diproses.</p>
                        <a href="{{ route('admin.orders.index') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">Lihat Order</a>
                    </div>
                    <div class="p-6 rounded-lg shadow-sm border border-green-100 bg-green-50">
                        <h3 class="font-bold text-green-800 mb-2">Manajemen Stok</h3>
                        <p class="text-sm text-green-600 mb-4">Cek stok menipis dan lakukan restock ke supplier.</p>
                        <a href="{{ route('admin.products.index') }}" class="inline-block bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">Cek Stok</a>
                    </div>
                    <div class="p-6 rounded-lg shadow-sm border border-purple-100 bg-purple-50">
                        <h3 class="font-bold text-purple-800 mb-2">Penetapan Harga</h3>
                        <p class="text-sm text-purple-600 mb-4">Atur harga jual fix dari harga supplier untuk dapur.</p>
                        <a href="{{ route('admin.products.index') }}" class="inline-block bg-purple-600 text-white px-4 py-2 rounded text-sm hover:bg-purple-700">Kelola Harga</a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4">Produk Butuh Perhatian (Stok Rendah)</h3>
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2">Nama Barang</th>
                                    <th class="px-4 py-2">Stok</th>
                                    <th class="px-4 py-2">Supplier</th>
                                    <th class="px-4 py-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\Product::where('stock', '<', 20)->take(5)->get() as $item)
                                <tr class="border-b">
                                    <td class="px-4 py-2 font-medium">{{ $item->name }}</td>
                                    <td class="px-4 py-2 text-red-600 font-bold">{{ $item->stock }}</td>
                                    <td class="px-4 py-2">{{ $item->supplier->name ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        <button class="text-blue-600 hover:underline">Restock</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- SUPPLIER VIEW -->
            @if(Auth::user()->role === 'supplier')
                <div class="rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-50 via-white to-amber-50 p-5">
                    <h3 class="text-lg font-bold text-amber-900">Area Supplier</h3>
                    <p class="text-sm text-amber-700 mt-1">Kelola daftar barang jual dan pantau nota yang terkait dengan suplai Anda.</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold">Barang Yang Saya Jual</h3>
                                <a href="{{ route('supplier.products.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 shadow-sm">
                                + Tambah Barang Baru
                                </a>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-700 uppercase">
                                    <tr>
                                        <th class="px-6 py-3">Nama Barang</th>
                                        <th class="px-6 py-3">Kategori</th>
                                        <th class="px-6 py-3">Harga Jual (Saya)</th>
                                        <th class="px-6 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(\App\Models\Product::where('supplier_id', Auth::id())->get() as $product)
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $product->name }}</td>
                                        <td class="px-6 py-4">{{ $product->category->name ?? '-' }}</td>
                                        <td class="px-6 py-4">Rp {{ number_format($product->supplier_price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $product->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($product->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                            Belum ada barang yang diinput. Silakan tambah barang.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-8">
                            <h4 class="text-base font-bold mb-3">Nota Terkait Supplier Saya</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50 text-gray-700 uppercase">
                                        <tr>
                                            <th class="px-4 py-2">Order</th>
                                            <th class="px-4 py-2">Tanggal</th>
                                            <th class="px-4 py-2">Mitra Dapur</th>
                                            <th class="px-4 py-2">Status</th>
                                            <th class="px-4 py-2 text-right">Nota</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $supplierOrders = \App\Models\Order::whereHas('orderItems.product', function ($q) {
                                                $q->where('supplier_id', Auth::id());
                                            })->latest()->take(8)->get();
                                        @endphp
                                        @forelse($supplierOrders as $order)
                                            <tr class="border-b">
                                                <td class="px-4 py-2">#{{ $order->id }}</td>
                                                <td class="px-4 py-2">{{ $order->created_at->format('d M Y H:i') }}</td>
                                                <td class="px-4 py-2">{{ $order->user->name ?? '-' }}</td>
                                                <td class="px-4 py-2">{{ ucfirst($order->status) }}</td>
                                                <td class="px-4 py-2 text-right">
                                                    <a href="{{ route('supplier.orders.invoice', $order->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-semibold">Lihat Nota</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-4 py-3 text-center text-gray-500">Belum ada nota untuk supplier ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- MITRA DAPUR VIEW -->
            @if(Auth::user()->role === 'dapur')
                <div class="rounded-2xl border border-violet-200 bg-gradient-to-r from-violet-50 via-white to-violet-50 p-5">
                    <h3 class="text-lg font-bold text-violet-900">Area Mitra Dapur</h3>
                    <p class="text-sm text-violet-700 mt-1">Pilih bahan baku dengan mudah, lakukan order cepat, dan pantau riwayat pesanan.</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 mb-6">
                    <h3 class="text-lg font-bold mb-4">Katalog Bahan Baku</h3>

                    @php
                        $selectedCategoryId = request('category_id');
                        $dashboardCategories = \App\Models\Category::all();
                        $dashboardProducts = \App\Models\Product::where('status', 'active')
                            ->whereNotNull('price')
                            ->when($selectedCategoryId, function ($query) use ($selectedCategoryId) {
                                $query->where('category_id', $selectedCategoryId);
                            })
                            ->get();
                    @endphp
                    
                    <div class="flex gap-2 overflow-x-auto pb-4 mb-4">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-full text-sm whitespace-nowrap {{ $selectedCategoryId ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' : 'bg-gray-800 text-white' }}">Semua</a>
                        @foreach($dashboardCategories as $cat)
                            <a href="{{ route('dashboard', ['category_id' => $cat->id]) }}" class="px-4 py-2 rounded-full text-sm whitespace-nowrap {{ (string) $selectedCategoryId === (string) $cat->id ? 'bg-gray-800 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">{{ $cat->name }}</a>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @forelse($dashboardProducts as $product)
                        <div class="border rounded-xl p-4 hover:shadow-md transition bg-white flex flex-col h-full">
                            @if($product->image_url)
                                <div class="h-32 bg-gray-100 rounded-lg mb-4 overflow-hidden">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover" loading="lazy" onerror="this.style.display='none'; this.parentElement.nextElementSibling.style.display='flex';">
                                </div>
                                <div class="h-32 bg-gray-100 rounded-lg mb-4 items-center justify-center text-gray-400 hidden">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @else
                                <div class="h-32 bg-gray-100 rounded-lg mb-4 flex items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            <h4 class="font-bold text-gray-900 mb-1">{{ $product->name }}</h4>
                            <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ $product->description }}</p>
                            
                            <div class="mt-auto">
                                <div class="flex justify-between items-end mb-3">
                                    <div>
                                        <p class="text-xs text-gray-500">Harga per unit</p>
                                        <p class="text-lg font-bold text-indigo-700">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('dapur.orders.create') }}" class="block text-center w-full py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                                    + Order Sekarang
                                </a>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center py-10 bg-gray-50 rounded-lg">
                            <p class="text-gray-500">Belum ada produk di kategori ini.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
