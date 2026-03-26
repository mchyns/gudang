<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Order Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="ui-panel overflow-hidden">
                <div class="p-6 text-gray-900">
                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('dapur.orders.create') }}" method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-3 ui-panel-soft p-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-500 mb-1">Cari Barang</label>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Contoh: bawang, telur, beras" class="w-full border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Kategori</label>
                            <select name="category_id" class="w-full border-gray-300 rounded-md text-sm">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-3 flex gap-2 justify-end">
                            <button class="ui-btn-primary">Terapkan Filter</button>
                            <a href="{{ route('dapur.orders.create') }}" class="ui-btn-ghost">Reset</a>
                        </div>
                    </form>

                    <form action="{{ route('dapur.orders.store') }}" method="POST" id="orderForm">
                        @csrf
                        
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Catatan Order (Opsional)</label>
                            <textarea name="note" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Contoh: Butuh cepat untuk acara nanti malam"></textarea>
                        </div>

                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pilih Produk</h3>

                        @if($products->isEmpty())
                            <div class="text-center py-10 bg-gray-50 rounded-lg">
                                <p class="text-gray-500">Belum ada produk yang tersedia untuk dipesan.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($products as $product)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow relative {{ $product->warehouse_stock <= 0 ? 'opacity-50 bg-gray-100' : 'bg-white' }}">
                                    @if($product->image_url)
                                        <div class="h-36 bg-gray-100 rounded-lg mb-3 overflow-hidden">
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover" loading="lazy" onerror="this.style.display='none'; this.parentElement.nextElementSibling.style.display='flex';">
                                        </div>
                                        <div class="h-36 bg-gray-100 rounded-lg mb-3 items-center justify-center text-gray-400 hidden">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @else
                                        <div class="h-36 bg-gray-100 rounded-lg mb-3 flex items-center justify-center text-gray-400">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif

                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="font-bold text-lg">{{ $product->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $product->category->name ?? 'Umum' }}</p>
                                            <div class="mt-1 flex items-center gap-2">
                                                <span class="text-[11px] px-2 py-0.5 rounded-full {{ $product->movement_type === 'fast' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ strtoupper($product->movement_type) }}</span>
                                            </div>
                                        </div>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                            Harga Gudang: Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-500 text-sm mb-4 h-10 overflow-hidden">{{ Str::limit($product->description, 60) }}</p>
                                    
                                    <div class="flex justify-between items-center mt-auto">
                                        <div class="text-sm">
                                            Stok Gudang: <span class="font-bold {{ $product->warehouse_stock < 5 ? 'text-red-600' : 'text-green-600' }}">{{ $product->warehouse_stock }}</span>
                                        </div>

                                        @if($product->warehouse_stock > 0)
                                            <div class="flex items-center space-x-2">
                                                <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $product->id }}" disabled class="product-id-input">
                                                <input type="number" 
                                                    name="items[{{ $loop->index }}][quantity]" 
                                                    min="0" 
                                                    max="{{ $product->warehouse_stock }}" 
                                                    class="w-20 border rounded px-2 py-1 text-right quantity-input" 
                                                    placeholder="0"
                                                    data-index="{{ $loop->index }}">
                                            </div>
                                        @else
                                            <span class="text-red-500 text-sm font-bold">Habis</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-8 flex justify-end">
                            <button type="submit" class="ui-btn-primary font-bold py-3 px-6 shadow-lg flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Pesan Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            const inputs = document.querySelectorAll('.quantity-input');
            let hasItems = false;

            inputs.forEach(input => {
                const index = input.getAttribute('data-index');
                const productIdInput = document.querySelector(`input[name="items[${index}][product_id]"]`);
                
                if (input.value > 0) {
                    hasItems = true;
                    // Enable the inputs so they are included in POST
                    input.disabled = false;
                    productIdInput.disabled = false;
                } else {
                    // Disable inputs with 0 quantity so they are NOT sent
                    input.disabled = true;
                    productIdInput.disabled = true;
                }
            });

            if (!hasItems) {
                e.preventDefault();
                alert('Pilih minimal satu produk untuk dipesan.');
                // Re-enable everything to avoid confusion if they cancel alert (though alert blocks)
                // Actually re-enabling 0 inputs is fine as long as we validate.
                // Resetting disabled state for UI consistency
                inputs.forEach(input => {
                    const index = input.getAttribute('data-index');
                    if (input.value <= 0) {
                       input.disabled = false; 
                    }
                });
            }
        });

        // Add visual feedback
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('input', function() {
                const card = this.closest('.border');
                if (this.value > 0) {
                    card.classList.add('ring-2', 'ring-indigo-500', 'bg-indigo-50');
                    card.classList.remove('bg-white');
                } else {
                    card.classList.remove('ring-2', 'ring-indigo-500', 'bg-indigo-50');
                    card.classList.add('bg-white');
                }
            });
        });
    </script>
</x-app-layout>
