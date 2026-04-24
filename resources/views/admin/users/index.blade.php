@extends('layouts.admin')

@section('title', 'Manajemen User')
@section('breadcrumb', 'User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        Manajemen Pengguna
        @if ($pendingDistributors > 0)
            <span class="badge bg-warning text-dark ms-2" style="font-size:.75rem">
                {{ $pendingDistributors }} distributor menunggu verifikasi
            </span>
        @endif
    </h4>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-person-plus me-1"></i> Tambah User
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Filter --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Cari nama, email, telepon..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="role" class="form-select form-select-sm">
                    <option value="">Semua Role</option>
                    <option value="admin"       @selected(request('role')==='admin')>Admin</option>
                    <option value="consumer"    @selected(request('role')==='consumer')>Consumer</option>
                    <option value="distributor" @selected(request('role')==='distributor')>Distributor</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="form-check ms-1">
                    <input class="form-check-input" type="checkbox" name="unverified"
                           id="unverified" value="1" @checked(request('unverified'))>
                    <label class="form-check-label small" for="unverified">
                        Distributor belum terverifikasi
                    </label>
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th class="text-center">Role</th>
                        <th class="text-center">Verifikasi</th>
                        <th class="text-center">Total Order</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr>
                        <td class="text-muted">{{ $users->firstItem() + $loop->index }}</td>
                        <td>
                            <a href="{{ route('admin.users.show', $user) }}"
                               class="fw-semibold text-decoration-none text-dark">
                                {{ $user->name }}
                            </a>
                            @if ($user->company_name)
                                <div class="text-muted" style="font-size:.8rem">
                                    <i class="bi bi-building"></i> {{ $user->company_name }}
                                </div>
                            @endif
                        </td>
                        <td class="small">{{ $user->email }}</td>
                        <td class="small">{{ $user->phone ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $user->role_badge_class }}">
                                {{ $user->role_label }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if ($user->role === 'distributor')
                                @if ($user->is_verified)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Terverifikasi
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock me-1"></i>Menunggu
                                    </span>
                                @endif
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">
                                {{ $user->orders_count }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            {{-- Tombol verifikasi distributor --}}
                            @if ($user->role === 'distributor')
                                <form action="{{ route('admin.users.verify', $user) }}"
                                      method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="btn btn-sm {{ $user->is_verified ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                        title="{{ $user->is_verified ? 'Cabut verifikasi' : 'Verifikasi' }}">
                                        <i class="bi bi-{{ $user->is_verified ? 'x-circle' : 'check-circle' }}"></i>
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>

                            @if ($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Tidak ada data pengguna.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if ($users->hasPages())
    <div class="mt-3">{{ $users->links() }}</div>
@endif
@endsection