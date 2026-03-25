<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Mitra Dapur & Supplier</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="ui-panel p-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Role Mitra</label>
                        <select name="role" class="w-full border-gray-300 rounded-md text-sm">
                            <option value="">Semua</option>
                            <option value="supplier" @selected($roleFilter === 'supplier')>Supplier</option>
                            <option value="dapur" @selected($roleFilter === 'dapur')>Dapur</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs text-gray-500 mb-1">Cari Nama / Email / HP / Alamat</label>
                        <input type="text" name="q" value="{{ $search }}" class="w-full border-gray-300 rounded-md text-sm" placeholder="Ketik kata kunci...">
                    </div>
                    <div class="flex items-end gap-2">
                        <button class="ui-btn-primary text-sm">Terapkan</button>
                        <a href="{{ route('admin.partners.index') }}" class="ui-btn-ghost text-sm">Reset</a>
                    </div>
                </form>
            </div>

            <div class="ui-panel overflow-hidden">
                <div class="ui-table-wrap">
                    <table class="min-w-[1200px] w-full text-sm ui-table">
                        <thead>
                            <tr>
                                <th class="text-left">Nama</th>
                                <th class="text-left">Role</th>
                                <th class="text-left">Email</th>
                                <th class="text-left">Nomor HP</th>
                                <th class="text-left">Alamat</th>
                                <th class="text-left">Ringkasan</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($partners as $partner)
                                <tr>
                                    <td class="font-semibold text-gray-900">{{ $partner->name }}</td>
                                    <td>
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $partner->role === 'supplier' ? 'bg-amber-100 text-amber-800' : 'bg-violet-100 text-violet-800' }}">
                                            {{ ucfirst($partner->role) }}
                                        </span>
                                    </td>
                                    <td>{{ $partner->email }}</td>
                                    <td>{{ $partner->phone ?: '-' }}</td>
                                    <td class="max-w-[300px]">{{ $partner->address ?: '-' }}</td>
                                    <td>
                                        @if($partner->role === 'supplier')
                                            Produk: {{ $partner->supplied_products_count }}
                                        @else
                                            Order: {{ $partner->orders_count }}
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.partners.show', $partner) }}" class="ui-btn-primary text-xs">Lihat Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-gray-500 py-6">Belum ada data mitra.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $partners->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
