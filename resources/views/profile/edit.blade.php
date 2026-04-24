{{--
    Halaman Profil Pengguna

    Layout yang dipakai berbeda berdasarkan role:
    - admin       -> layouts.admin  (sidebar gelap)
    - consumer    -> layouts.shop   (navbar toko)
    - distributor -> layouts.shop   (navbar toko)

    Kenapa tidak satu layout saja?
    Admin berada di konteks "panel manajemen" sehingga wajar memakai
    layout admin. Consumer dan distributor berada di konteks "toko"
    sehingga lebih konsisten memakai layout toko.

    ProfileController (dari Breeze) mengirim variabel $user ke view ini,
    sehingga form bisa diisi ulang dengan data yang sudah ada.
--}}

@php
    // Pilih layout berdasarkan role user yang sedang login
    $layout = auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.shop';
@endphp

@extends($layout)

@section('title', 'Profil Saya')
@section('breadcrumb', 'Profil')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        <h4 class="fw-bold mb-4">
            <i class="bi bi-person-circle me-2"></i>Profil Saya
        </h4>

        {{-- ═══ 
            BAGIAN 1 — Informasi Profil
            Route: PATCH /profile  → ProfileController@update
        ═══ --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header fw-semibold bg-white border-bottom">
                Informasi Profil
                <small class="text-muted fw-normal ms-2">
                    Perbarui nama, email, dan data kontak.
                </small>
            </div>
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- ════
            BAGIAN 2 — Ubah Password
            Route: PUT /password  → PasswordController@update
        ════ --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header fw-semibold bg-white border-bottom">
                Ubah Password
                <small class="text-muted fw-normal ms-2">
                    Gunakan password panjang dan acak agar akun lebih aman.
                </small>
            </div>
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- ═══
            BAGIAN 3 — Hapus Akun
            Route: DELETE /profile  → ProfileController@destroy

            Hanya ditampilkan untuk consumer & distributor.
            Admin tidak bisa menghapus akunnya sendiri dari sini
            (dilakukan lewat panel admin).
        ═══ --}}
        @if (auth()->user()->role !== 'admin')
            <div class="card border-danger border-0 shadow-sm mb-4">
                <div class="card-header fw-semibold bg-white border-bottom text-danger">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Zona Berbahaya
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
