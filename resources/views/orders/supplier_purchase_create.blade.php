<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Belanja Gudang ke Supplier</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="ui-panel p-6">
                @if(session('error'))
                    <div class="mb-4 bg-rose-100 border border-rose-200 text-rose-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="mb-4 flex flex-wrap items-center justify-end gap-2">
                    <form method="GET" action="{{ route('admin.orders.supplier-purchase.invoice-daily') }}" target="_blank" class="flex items-center gap-2">
                        <input type="date" name="date" value="{{ now()->toDateString() }}" class="border-gray-300 rounded-md text-sm">
                        <button class="ui-btn-ghost text-sm" type="submit">Buka Nota Gabungan</button>
                    </form>
                </div>

                <form action="{{ route('admin.orders.supplier-purchase.create') }}" method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-3 ui-panel-soft p-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-500 mb-1">Cari Barang</label>
                        <input type="text" name="q" value="{{ request('q') }}" class="w-full border-gray-300 rounded-md text-sm" placeholder="Contoh: bawang, beras, cabai">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Supplier</label>
                        <select name="supplier_id" class="w-full border-gray-300 rounded-md text-sm">
                            <option value="">Semua Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected((string) request('supplier_id') === (string) $supplier->id)>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
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
                    <div class="md:col-span-4 flex justify-end gap-2">
                        <button class="ui-btn-primary">Filter</button>
                        <a href="{{ route('admin.orders.supplier-purchase.create') }}" class="ui-btn-ghost">Reset</a>
                    </div>
                </form>

                <form action="{{ route('admin.orders.supplier-purchase.store') }}" method="POST" id="supplierPurchaseForm">
                    @csrf

                    <div class="mb-5">
                        <label class="block text-sm text-gray-700 mb-1">Catatan Pembelian Gudang (Opsional)</label>
                        <textarea name="note" rows="2" class="w-full border-gray-300 rounded-md" placeholder="Contoh: Prioritas kirim pagi, pisahkan per supplier."></textarea>
                    </div>

                    @if($products->isEmpty())
                        <div class="text-center py-10 bg-gray-50 rounded-lg text-gray-500">Tidak ada produk supplier yang cocok dengan filter.</div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach($products as $product)
                                <div class="border rounded-lg p-4 bg-white hover:shadow-sm transition-shadow">
                                    <div class="flex justify-between items-start gap-2 mb-2">
                                        <div>
                                            <h4 class="font-bold text-gray-900">{{ $product->name }}</h4>
                                            <p class="text-xs text-gray-500">{{ $product->category->name ?? '-' }}</p>
                                            <p class="text-xs text-indigo-600 font-semibold">Supplier: {{ $product->supplier->name ?? '-' }}</p>
                                        </div>
                                        <span class="text-xs px-2 py-1 rounded bg-amber-100 text-amber-700">Rp {{ number_format($product->supplier_price, 0, ',', '.') }}</span>
                                    </div>

                                    <div class="text-xs text-gray-500 mb-4">Satuan: {{ $product->unit }}</div>

                                    <div class="flex items-center justify-between gap-2">
                                        <label class="text-xs text-gray-500">Qty beli</label>
                                        <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $product->id }}" disabled class="product-id-input">
                                        <input type="number" min="0" name="items[{{ $loop->index }}][quantity]" class="w-24 border rounded px-2 py-1 text-right quantity-input" data-index="{{ $loop->index }}" placeholder="0">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="ui-btn-primary px-6 py-3">Simpan Pembelian Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('supplierPurchaseForm')?.addEventListener('submit', function (e) {
            const inputs = document.querySelectorAll('.quantity-input');
            let hasItems = false;

            inputs.forEach((input) => {
                const index = input.dataset.index;
                const productIdInput = document.querySelector(`input[name="items[${index}][product_id]"]`);
                if (Number(input.value || 0) > 0) {
                    hasItems = true;
                    input.disabled = false;
                    productIdInput.disabled = false;
                } else {
                    input.disabled = true;
                    productIdInput.disabled = true;
                }
            });

            if (!hasItems) {
                e.preventDefault();
                alert('Pilih minimal satu barang untuk dibeli dari supplier.');
                inputs.forEach((input) => {
                    input.disabled = false;
                });
            }
        });
    </script>
</x-app-layout>
