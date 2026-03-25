
<x-app-layout>
    {{-- <x-slot name="header">
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
    </x-slot> --}}

    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-100 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            
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
                    <div class="p-4 sm:p-6">
                        <h3 class="text-lg font-bold mb-4">Produk Butuh Perhatian (Stok Rendah)</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[520px] text-sm text-left">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2">Nama Barang</th>
                                        <th class="px-4 py-2">Stok</th>
                                        <th class="px-4 py-2">Supplier</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(\App\Models\Product::where('stock', '<', 20)->take(5)->get() as $item)
                                    <tr class="border-b">
                                        <td class="px-4 py-2 font-medium">{{ $item->name }}</td>
                                        <td class="px-4 py-2 text-red-600 font-bold">{{ $item->stock }}</td>
                                        <td class="px-4 py-2">{{ $item->supplier->name ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
                                            <th class="px-4 py-2">Penerima</th>
                                            <th class="px-4 py-2">Catatan Supplier ke Gudang</th>
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
                                                <td class="px-4 py-2">Gudang</td>
                                                <td class="px-4 py-2 min-w-[280px]">
                                                    <form action="{{ route('supplier.orders.update-note', $order) }}" method="POST" class="flex gap-2 items-center">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="text" name="supplier_note" value="{{ old('supplier_note', $order->supplier_note) }}" class="w-full border-gray-300 rounded-md text-xs" placeholder="Catatan untuk gudang (opsional)">
                                                        <button type="submit" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded text-[11px] font-semibold text-slate-700 whitespace-nowrap">Simpan</button>
                                                    </form>
                                                </td>
                                                <td class="px-4 py-2">{{ ucfirst($order->status) }}</td>
                                                <td class="px-4 py-2 text-right">
                                                    <a href="{{ route('supplier.orders.invoice', $order->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-semibold">Lihat Nota</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-4 py-3 text-center text-gray-500">Belum ada nota untuk supplier ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

           @if(Auth::user()->role === 'dapur')
    <div class="relative overflow-hidden rounded-3xl border border-blue-100 bg-white p-5 sm:p-8 shadow-sm mb-8">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight" style="font-family: 'Syne', sans-serif;">
                    AREA MITRA <span class="text-blue-700">DAPUR</span>
                </h3>
                <p class="text-slate-500 text-sm font-medium mt-2 max-w-xl leading-relaxed">
                    Kelola kebutuhan dapur Anda dengan standar profesional. Pilih bahan baku terbaik dan pantau pesanan Anda secara langsung.
                </p>
            </div>
            <div>
                <a href="{{ route('dapur.orders.my_orders') }}" class="inline-flex items-center px-6 py-3 bg-slate-50 text-slate-700 rounded-2xl text-xs font-bold uppercase tracking-widest hover:bg-slate-100 transition-all border border-slate-200 shadow-sm">
                    Riwayat Pesanan 📜
                </a>
            </div>
        </div>
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-50 rounded-full blur-3xl opacity-60"></div>
    </div>

    <div class="bg-white p-5 sm:p-8 rounded-[2rem] shadow-sm border border-slate-100 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Katalog Bahan Baku</h3>
            
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
            
            <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                <a href="{{ route('dashboard') }}" 
                   class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300 whitespace-nowrap
                   {{ !$selectedCategoryId ? 'bg-blue-700 text-white shadow-lg shadow-blue-200' : 'bg-slate-50 text-slate-400 hover:bg-slate-100 border border-slate-100' }}">
                   Semua
                </a>
                @foreach($dashboardCategories as $cat)
                    <a href="{{ route('dashboard', ['category_id' => $cat->id]) }}" 
                       class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300 whitespace-nowrap
                       {{ (string) $selectedCategoryId === (string) $cat->id ? 'bg-blue-700 text-white shadow-lg shadow-blue-200' : 'bg-slate-50 text-slate-400 hover:bg-slate-100 border border-slate-100' }}">
                       {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-8">
            @forelse($dashboardProducts as $product)
            <div class="group border border-slate-50 rounded-3xl p-5 hover:shadow-2xl hover:shadow-blue-100/50 transition-all duration-500 bg-white flex flex-col h-full relative">
                
                <div class="h-44 bg-slate-50 rounded-2xl mb-5 overflow-hidden border border-slate-50 relative">
                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" loading="lazy">
                    @else
                        <div class="h-full w-full flex items-center justify-center text-slate-200">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif
                    
                    <div class="absolute top-3 right-3">
                        <span class="bg-white/80 backdrop-blur px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-tighter text-blue-700 shadow-sm border border-blue-50">
                            {{ $product->category->name ?? 'Stok' }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-col flex-grow">
                    <h4 class="font-bold text-slate-900 group-hover:text-blue-700 transition-colors duration-300 text-lg leading-tight mb-2 uppercase">{{ $product->name }}</h4>
                    <p class="text-xs text-slate-400 mb-6 line-clamp-2 font-medium leading-relaxed">{{ $product->description }}</p>
                    
                    <div class="mt-auto pt-5 border-t border-slate-50">
                        <div class="flex justify-between items-center mb-5">
                            <div>
                                <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Harga / Unit</p>
                                <p class="text-xl font-black text-slate-900">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.6)] animate-pulse"></div>
                                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-tighter">Tersedia</span>
                            </div>
                        </div>
                        
                        <a href="{{ route('dapur.orders.create') }}" 
                           class="flex items-center justify-center gap-2 w-full py-4 bg-blue-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-800 hover:shadow-xl hover:shadow-blue-200 transition-all duration-300">
                           <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                           Pesan Sekarang
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-20 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-100">
                <span class="text-4xl block mb-4">📦</span>
                <p class="text-slate-500 uppercase tracking-widest text-xs font-black">Belum ada produk di kategori ini.</p>
            </div>
            @endforelse
        </div>
    </div>
@endif
        </div>
    </div>
</x-app-layout>
