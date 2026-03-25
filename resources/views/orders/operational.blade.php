<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Rincian Operasional Order #{{ $order->id }}</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="ui-panel p-6">
                <form method="POST" action="{{ route('admin.orders.operational.update', $order) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">1. Bensin</label>
                            <input type="text" inputmode="numeric" name="operational_bensin" value="{{ old('operational_bensin', $order->operational_bensin) }}" class="w-full border-gray-300 rounded-md js-nominal" placeholder="Boleh kosong">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">2. Kuli</label>
                            <input type="text" inputmode="numeric" name="operational_kuli" value="{{ old('operational_kuli', $order->operational_kuli) }}" class="w-full border-gray-300 rounded-md js-nominal" placeholder="Boleh kosong">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">3. Makan Minum</label>
                            <input type="text" inputmode="numeric" name="operational_makan_minum" value="{{ old('operational_makan_minum', $order->operational_makan_minum) }}" class="w-full border-gray-300 rounded-md js-nominal" placeholder="Boleh kosong">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">4. Listrik</label>
                            <input type="text" inputmode="numeric" name="operational_listrik" value="{{ old('operational_listrik', $order->operational_listrik) }}" class="w-full border-gray-300 rounded-md js-nominal" placeholder="Boleh kosong">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-700 mb-1">5. Wifi</label>
                            <input type="text" inputmode="numeric" name="operational_wifi" value="{{ old('operational_wifi', $order->operational_wifi) }}" class="w-full border-gray-300 rounded-md js-nominal" placeholder="Boleh kosong">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-2">
                        <a href="{{ route('admin.orders.index') }}" class="ui-btn-ghost">Kembali</a>
                        <button class="ui-btn-primary">Simpan Operasional</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
