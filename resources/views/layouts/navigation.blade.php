@php
    $role = Auth::user()->role;
    
    // Konfigurasi tema premium: Semua disamakan dengan basis Indigo Admin
    $themes = [
        'supplier' => [
            'bg' => 'bg-indigo-900',
            'brand' => 'text-white',
            'link' => 'text-indigo-100 hover:text-white',
            'active' => 'bg-white/10 text-white border-white',
        ],
        'admin' => [
            'bg' => 'bg-indigo-900',
            'brand' => 'text-white',
            'link' => 'text-indigo-100 hover:text-white',
            'active' => 'bg-white/10 text-white border-white',
        ],
        'superadmin' => [
            'bg' => 'bg-indigo-900',
            'brand' => 'text-white',
            'link' => 'text-indigo-100 hover:text-white',
            'active' => 'bg-white/10 text-white border-white',
        ],
        'dapur' => [
            'bg' => 'bg-indigo-900',
            'brand' => 'text-white',
            'link' => 'text-indigo-100 hover:text-white',
            'active' => 'bg-white/10 text-white border-white',
        ],
        'default' => [
            'bg' => 'bg-white/95',
            'brand' => 'text-gray-900',
            'link' => 'text-gray-600 hover:text-indigo-600',
            'active' => 'bg-indigo-50 text-indigo-600 border-indigo-600',
        ]
    ];

    $current = $themes[$role] ?? $themes['default'];

    // Daftar Menu (Otomatis muncul di Inspect/Mobile)
    $navItems = [
        ['route' => 'dashboard', 'label' => 'Home', 'icon' => '🏠', 'roles' => ['admin', 'superadmin', 'supplier', 'dapur']],
        ['route' => 'admin.products.index', 'label' => 'Inventory', 'icon' => '📦', 'roles' => ['admin', 'superadmin']],
        ['route' => 'admin.orders.index', 'label' => 'Orders', 'icon' => '🛒', 'roles' => ['admin', 'superadmin']],
        ['route' => 'admin.finance.index', 'label' => 'Gaji', 'icon' => '💰', 'roles' => ['admin']],
        ['route' => 'supplier.products.index', 'label' => 'Stock', 'icon' => '📦', 'roles' => ['supplier']],
        ['route' => 'dapur.orders.create', 'label' => 'Order', 'icon' => '➕', 'roles' => ['dapur']],
        ['route' => 'dapur.orders.my_orders', 'label' => 'Riwayat', 'icon' => '📜', 'roles' => ['dapur']],
    ];
@endphp

<nav x-data="{ open: false }" 
    class="{{ $current['bg'] }} sticky top-0 z-50 border-b border-white/10 shadow-2xl transition-all duration-500">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 sm:h-20">
            <div class="flex items-center">
                <div class="shrink-0 flex items-center group">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 sm:gap-3">
                        <div class="relative bg-white p-1.5 sm:p-2 rounded-lg sm:rounded-xl shadow-lg transform group-hover:scale-105 group-hover:rotate-3 transition-all duration-300">
                            <img src="{{ asset('images/logo.png') }}" alt="Hikarisou Logo" class="block h-8 sm:h-11 w-auto">
                        </div>
                        <div class="flex flex-col ml-1 sm:ml-3">
                            <span class="text-white tracking-tight leading-none uppercase" 
                                style="font-family: 'Syne', sans-serif; font-size: 0.95rem; font-weight: 800; letter-spacing: -0.01em;">
                                HIKARI<span class="opacity-70 font-light text-indigo-200">SOU</span>
                            </span>
                            
                            <span class="hidden sm:block text-white text-[9px] tracking-[0.4em] uppercase opacity-50 font-medium mt-0.5">
                                Warehouse Management System
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(Auth::user()->hasRole(['admin', 'superadmin']))
                        <x-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                            {{ __('Kelola Produk') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                            {{ __('Kelola Pesanan') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.partners.index')" :active="request()->routeIs('admin.partners.*')">
                            {{ __('Daftar Mitra') }}
                        </x-nav-link>
                        @if(Auth::user()->hasRole('admin'))
                            <x-nav-link :href="route('admin.finance.index')" :active="request()->routeIs('admin.finance.*')">
                                {{ __('Laba & Gaji') }}
                            </x-nav-link>
                        @endif
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center">
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 pl-3 pr-1 py-1 rounded-full border border-white/10 bg-white/5 hover:bg-white/10 transition-all duration-300">
                            <span class="text-sm font-bold {{ $current['brand'] }} ml-2">{{ Auth::user()->name }}</span>
                            <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-white/20 to-white/5 flex items-center justify-center border border-white/20">
                                <span class="text-xs font-black text-white">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest leading-none mb-1">Signed in as</p>
                            <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <x-dropdown-link :href="route('profile.edit')" class="py-3">
                            👤 {{ __('Pengaturan') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" 
                                            class="text-red-600 font-bold py-3 hover:bg-red-50" 
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                🚪 {{ __('Logout') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-xl {{ $current['brand'] }} hover:bg-white/10 transition-all">
                    <svg class="h-7 w-7" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(Auth::user()->hasRole(['admin', 'superadmin']))
                <x-responsive-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                    {{ __('Kelola Produk') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                    {{ __('Kelola Pesanan') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.partners.index')" :active="request()->routeIs('admin.partners.*')">
                    {{ __('Daftar Mitra') }}
                </x-responsive-nav-link>
                @if(Auth::user()->hasRole('admin'))
                    <x-responsive-nav-link :href="route('admin.finance.index')" :active="request()->routeIs('admin.finance.*')">
                        {{ __('Laba & Gaji') }}
                    </x-responsive-nav-link>
                @endif
            @endif

            <div class="mt-4 pt-4 border-t border-white/10">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-4 px-4 py-4 rounded-xl text-red-200 font-bold hover:bg-red-500/20 text-left transition-all">
                        <span>🚪</span> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>