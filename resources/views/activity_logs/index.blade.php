<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Log Aktivitas Karyawan</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(Auth::user()->hasRole('admin'))
                <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-lg px-4 py-3 text-sm">
                    Anda hanya dapat melihat aktivitas user Supplier dan Dapur.
                </div>
            @elseif(Auth::user()->hasRole('superadmin'))
                <div class="bg-indigo-50 border border-indigo-200 text-indigo-800 rounded-lg px-4 py-3 text-sm">
                    Super Admin dapat melihat seluruh aktivitas semua role.
                </div>
            @endif

            <div class="ui-panel p-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Filter Aksi</label>
                        <select name="action" class="w-full border-gray-300 rounded-md text-sm">
                            <option value="">Semua Aksi</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Filter User</label>
                        <select name="user_id" class="w-full border-gray-300 rounded-md text-sm">
                            <option value="">Semua User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2 flex items-end gap-2">
                        <button class="ui-btn-primary text-sm">Terapkan</button>
                        <a href="{{ route('admin.activity-logs.index') }}" class="ui-btn-ghost text-sm">Reset</a>
                    </div>
                </form>
            </div>

            <div class="ui-panel overflow-hidden">
                <div class="ui-table-wrap">
                    <table class="min-w-full divide-y divide-gray-200 text-sm ui-table">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Waktu</th>
                                <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">User</th>
                                <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Aksi</th>
                                <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Deskripsi</th>
                                <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($logs as $log)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                    <td class="px-4 py-3">{{ $log->user->name ?? 'System' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-semibold">{{ $log->action }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $log->description }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $log->ip_address ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada aktivitas tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $logs->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
