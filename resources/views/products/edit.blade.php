<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Produk') }}: {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="ui-panel overflow-hidden">
                <div class="p-6 text-gray-900">
                    @if($errors->any())
                        <div class="mb-4 bg-rose-100 border border-rose-200 text-rose-700 px-4 py-3 rounded">
                            <ul class="list-disc pl-5 text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ auth()->user()->hasRole('supplier') ? route('supplier.products.update', $product) : route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Section for Everyone (Read-only for Admin, Editable for Supplier) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 border-b pb-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Produk (Supplier)</h3>
                                
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Produk</label>
                                    <input type="text" name="name" value="{{ old('name', $product->name) }}" 
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ auth()->user()->hasRole('admin') ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                        {{ auth()->user()->hasRole('admin') ? 'readonly' : '' }}>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Harga dari Supplier</label>
                                    <input type="text" inputmode="numeric" name="supplier_price" value="{{ old('supplier_price', $product->supplier_price) }}" 
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline js-nominal {{ auth()->user()->hasRole('admin') ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                        {{ auth()->user()->hasRole('admin') ? 'readonly' : '' }}>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Satuan</label>
                                    <input type="text" name="unit" value="{{ old('unit', $product->unit) }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ auth()->user()->hasRole('admin') ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                        {{ auth()->user()->hasRole('admin') ? 'readonly' : '' }}>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                                    <select name="category_id"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ auth()->user()->hasRole('admin') ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                        {{ auth()->user()->hasRole('admin') ? 'disabled' : '' }}>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(auth()->user()->hasRole('admin'))
                                        <input type="hidden" name="category_id" value="{{ $product->category_id }}">
                                    @endif
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Stok Tersedia</label>
                                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" 
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ auth()->user()->hasRole('admin') ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                        {{ auth()->user()->hasRole('admin') ? 'readonly' : '' }}>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                                    <textarea name="description" 
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ auth()->user()->hasRole('admin') ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                        {{ auth()->user()->hasRole('admin') ? 'readonly' : '' }}>{{ old('description', $product->description) }}</textarea>
                                </div>

                                @if(auth()->user()->hasRole('supplier'))
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Gambar Produk (Opsional)</label>
                                        <input type="file" name="image" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <p class="text-xs text-gray-500 mt-1">Upload ulang gambar jika sebelumnya belum tampil.</p>
                                        @error('image') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                            </div>

                            {{-- Section for Admin Logic --}}
                            @if(auth()->user()->hasRole('admin'))
                            <div class="ui-panel-soft p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-blue-900 mb-4">Pengaturan Admin (Harga Jual)</h3>
                                
                                <div class="mb-4">
                                    <label class="block text-blue-800 text-sm font-bold mb-2">Harga Jual (Fix Price untuk Dapur)</label>
                                    <input type="text" inputmode="numeric" name="price" value="{{ old('price', $product->price) }}" class="shadow appearance-none border border-blue-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-2 js-nominal">
                                    <p class="text-xs text-blue-600">Harga ini yang akan dilihat oleh pihak Dapur/Kitchen.</p>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-blue-800 text-sm font-bold mb-2">Status Produk</label>
                                    <select name="status" class="shadow appearance-none border border-blue-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Active (Bisa Dipesan)</option>
                                        <option value="inactive" {{ $product->status === 'inactive' ? 'selected' : '' }}>Inactive (Sembunyikan)</option>
                                        <option value="pending" {{ $product->status === 'pending' ? 'selected' : '' }}>Pending (Menunggu Review)</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-blue-800 text-sm font-bold mb-2">Tipe Pergerakan Barang</label>
                                    <select name="movement_type" class="shadow appearance-none border border-blue-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="fast" {{ $product->movement_type === 'fast' ? 'selected' : '' }}>Fast Moving</option>
                                        <option value="slow" {{ $product->movement_type === 'slow' ? 'selected' : '' }}>Slow Moving</option>
                                    </select>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button class="ui-btn-primary font-bold py-2 px-4 focus:outline-none focus:shadow-outline" type="submit">
                                {{ auth()->user()->hasRole('admin') ? 'Simpan Harga Fix' : 'Update Produk' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
