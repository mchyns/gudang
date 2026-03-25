<aside class="sidebar" id="appSidebar">
	<a href="{{ route('admin.users.index') }}" class="sb-brand">
		<div class="sb-logo-mark">
			<svg viewBox="0 0 24 24">
				<path d="M21 8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16V8z"/>
				<polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
			</svg>
		</div>
		<div class="sb-brand-text">
			<div class="sb-brand-name"><em>HIKARI</em> Logistik</div>
			<div class="sb-brand-sub">Admin Panel</div>
		</div>
	</a>

	<nav class="sb-nav">
		<div class="sb-section-label">Menu Utama</div>

		<a href="{{ route('admin.users.index') }}"
		   class="sb-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
		   data-label="Kelola Pengguna">
			<svg class="sb-icon" viewBox="0 0 24 24">
				<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
				<circle cx="9" cy="7" r="4"/>
				<path d="M23 21v-2a4 4 0 00-3-3.87"/>
				<path d="M16 3.13a4 4 0 010 7.75"/>
			</svg>
			<span class="sb-link-label">Kelola Pengguna</span>
		</a>

		<a href="{{ route('admin.products.index') }}"
		   class="sb-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
		   data-label="Kelola Produk">
			<svg class="sb-icon" viewBox="0 0 24 24">
				<path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>
				<polyline points="7.5 4.21 12 6.81 16.5 4.21"/>
				<polyline points="7.5 19.79 7.5 14.6 3 12"/>
			</svg>
			<span class="sb-link-label">Kelola Produk</span>
		</a>

		<a href="{{ route('admin.orders.index') }}"
		   class="sb-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
		   data-label="Kelola Pesanan">
			<svg class="sb-icon" viewBox="0 0 24 24">
				<circle cx="9" cy="21" r="1"/>
				<circle cx="20" cy="21" r="1"/>
				<path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
			</svg>
			<span class="sb-link-label">Kelola Pesanan</span>
		</a>

		<a href="{{ route('admin.partners.index') }}"
		   class="sb-link {{ request()->routeIs('admin.partners.*') ? 'active' : '' }}"
		   data-label="Daftar Mitra">
			<svg class="sb-icon" viewBox="0 0 24 24">
				<path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/>
				<circle cx="9" cy="7" r="4"/>
				<path d="M22 21v-2a4 4 0 00-3-3.87"/>
				<path d="M16 3.13a4 4 0 010 7.75"/>
			</svg>
			<span class="sb-link-label">Daftar Mitra</span>
		</a>

		<a href="{{ route('admin.activity-logs.index') }}"
		   class="sb-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}"
		   data-label="Log Aktivitas">
			<svg class="sb-icon" viewBox="0 0 24 24">
				<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
			</svg>
			<span class="sb-link-label">Log Aktivitas</span>
		</a>

		@if(Auth::user()->hasRole('superadmin'))
			<a href="{{ route('superadmin.insights.index') }}"
			   class="sb-link {{ request()->routeIs('superadmin.insights.*') ? 'active' : '' }}"
			   data-label="Insight Superadmin">
				<svg class="sb-icon" viewBox="0 0 24 24">
					<line x1="18" y1="20" x2="18" y2="10"/>
					<line x1="12" y1="20" x2="12" y2="4"/>
					<line x1="6" y1="20" x2="6" y2="14"/>
				</svg>
				<span class="sb-link-label">Insight Superadmin</span>
			</a>
		@endif

		<div class="sb-divider"></div>
		<div class="sb-section-label">Akses Cepat</div>

		<a href="{{ route('dashboard') }}" class="sb-link" data-label="Dashboard Umum">
			<svg class="sb-icon" viewBox="0 0 24 24">
				<rect x="3" y="3" width="7" height="7"/>
				<rect x="14" y="3" width="7" height="7"/>
				<rect x="14" y="14" width="7" height="7"/>
				<rect x="3" y="14" width="7" height="7"/>
			</svg>
			<span class="sb-link-label">Dashboard Umum</span>
		</a>
	</nav>

	<div class="sb-footer">
		<div class="sb-user">
			<div class="sb-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
			<div class="sb-user-info">
				<div class="sb-user-name">{{ Auth::user()->name }}</div>
				<div class="sb-user-role">{{ Auth::user()->role }}</div>
			</div>

			<form method="POST" action="{{ route('logout') }}">
				@csrf
				<button type="submit" class="sb-logout-btn" title="Keluar">
					<svg viewBox="0 0 24 24">
						<path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
						<polyline points="16 17 21 12 16 7"/>
						<line x1="21" y1="12" x2="9" y2="12"/>
					</svg>
				</button>
			</form>
		</div>
	</div>
</aside>
