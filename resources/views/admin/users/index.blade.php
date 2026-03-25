@extends('admin.layouts.app')

@push('styles')
<style>
	.role-pill {
		display: inline-block;
		padding: 4px 10px;
		border-radius: 999px;
		font-size: 0.72rem;
		font-weight: 700;
		text-transform: uppercase;
		letter-spacing: .05em;
	}

	.role-pill.role-superadmin { background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; }
	.role-pill.role-admin { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
	.role-pill.role-supplier { background: #ecfeff; color: #155e75; border: 1px solid #bae6fd; }
	.role-pill.role-dapur { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
	.role-pill.role-default { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
</style>
@endpush

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:18px;flex-wrap:wrap;">
	<div>
		<h1 style="font-family:'Syne',sans-serif;font-size:1.35rem;color:#111827;letter-spacing:-0.01em;">Kelola Pengguna</h1>
		<p style="font-size:0.9rem;color:#6b7280;margin-top:4px;">Atur role user untuk akses Admin, Supplier, dan Dapur.</p>
	</div>
	<span style="font-size:0.8rem;color:#111FA2;background:#f0f2ff;border:1px solid rgba(17,31,162,0.12);padding:6px 10px;border-radius:999px;">
		Total User: {{ $users->count() }}
	</span>
</div>

<div style="background:#fff;border:1px solid rgba(17,31,162,0.08);border-radius:16px;overflow:hidden;box-shadow:0 8px 24px rgba(17,31,162,0.06);">
	<div style="overflow-x:auto;">
		<table style="width:100%;border-collapse:collapse;min-width:1100px;">
			<thead style="background:#f8f9ff;">
				<tr>
					<th style="text-align:left;padding:14px 16px;font-size:0.72rem;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;">Nama</th>
					<th style="text-align:left;padding:14px 16px;font-size:0.72rem;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;">Email</th>
					<th style="text-align:left;padding:14px 16px;font-size:0.72rem;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;">Nomor HP</th>
					<th style="text-align:left;padding:14px 16px;font-size:0.72rem;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;">Alamat</th>
					<th style="text-align:left;padding:14px 16px;font-size:0.72rem;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;">Role Saat Ini</th>
					<th style="text-align:left;padding:14px 16px;font-size:0.72rem;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;">Ubah Role</th>
					<th style="text-align:right;padding:14px 16px;font-size:0.72rem;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;">Aksi</th>
				</tr>
			</thead>
			<tbody>
				@forelse($users as $user)
					<tr style="border-top:1px solid rgba(17,31,162,0.06);">
						<td style="padding:14px 16px;">
							<div style="font-weight:600;color:#111827;">{{ $user->name }}</div>
							<div style="font-size:0.78rem;color:#9ca3af;">ID: {{ $user->id }}</div>
						</td>
						<td style="padding:14px 16px;color:#374151;">{{ $user->email }}</td>
						<td style="padding:14px 16px;color:#374151;white-space:nowrap;">{{ $user->phone ?: '-' }}</td>
						<td style="padding:14px 16px;color:#374151;max-width:280px;">{{ $user->address ?: '-' }}</td>
						<td style="padding:14px 16px;">
							@php
								$roleClass = in_array($user->role, ['superadmin', 'admin', 'supplier', 'dapur'])
									? 'role-' . $user->role
									: 'role-default';
							@endphp
							<span class="role-pill {{ $roleClass }}">
								{{ $user->role }}
							</span>
						</td>
						<td style="padding:14px 16px;">
							<form action="{{ route('admin.users.update-role', $user) }}" method="POST" style="display:flex;gap:8px;align-items:center;">
								@csrf
								@method('PATCH')
								<select name="role" style="border:1px solid rgba(17,31,162,0.2);border-radius:8px;padding:8px 10px;font-size:0.85rem;min-width:150px;background:#fff;">
									<option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
									@if(Auth::user()->role === 'superadmin' || $user->role === 'superadmin')
										<option value="superadmin" {{ $user->role === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
									@endif
									<option value="supplier" {{ $user->role === 'supplier' ? 'selected' : '' }}>Supplier</option>
									<option value="dapur" {{ $user->role === 'dapur' ? 'selected' : '' }}>Dapur</option>
								</select>
								<button type="submit" style="padding:8px 12px;border-radius:8px;border:1px solid #111FA2;background:#111FA2;color:#fff;font-size:0.8rem;font-weight:600;cursor:pointer;">
									Simpan
								</button>
							</form>
							@error('role')
								<div style="font-size:0.76rem;color:#dc2626;margin-top:6px;">{{ $message }}</div>
							@enderror
						</td>
						<td style="padding:14px 16px;text-align:right;font-size:0.78rem;color:#9ca3af;">
							{{ $user->updated_at?->format('d M Y H:i') ?? '-' }}
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="7" style="padding:28px 16px;text-align:center;color:#6b7280;">Belum ada user untuk dikelola.</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>
@endsection