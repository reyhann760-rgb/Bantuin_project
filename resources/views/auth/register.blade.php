@extends('layouts.app')

@section('title', 'Registrasi - Bantuin')

@section('content')

<div class="card">

    <h1>Daftar Bantuin</h1>

    <p>Buat akun untuk mulai menggunakan layanan Bantuin.</p>

    <form action="{{ route('register.store') }}" method="POST">

        @csrf

        <div class="form-group">
            <label>Nama Lengkap</label>

            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                placeholder="Masukkan nama lengkap"
                required
            >
        </div>

        <div class="form-group">
            <label>Email</label>

            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Masukkan email"
                required
            >
        </div>

        <div class="form-group">
            <label>Nomor Telepon (Opsional)</label>

            <input
                type="text"
                name="phone"
                value="{{ old('phone') }}"
                placeholder="Masukkan nomor telepon"
            >
        </div>

        <div class="form-group">
            <label>Daftar Sebagai</label>

            <select name="role" required>
                <option value="">-- Pilih Role --</option>

                <option value="customer"
                    {{ old('role') == 'customer' ? 'selected' : '' }}>
                    Customer
                </option>

                <option value="provider"
                    {{ old('role') == 'provider' ? 'selected' : '' }}>
                    Penyedia Jasa
                </option>
            </select>
        </div>

        <div class="form-group">
            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Minimal 8 karakter"
                required
            >
        </div>

        <div class="form-group">
            <label>Konfirmasi Password</label>

            <input
                type="password"
                name="password_confirmation"
                placeholder="Ulangi password"
                required
            >
        </div>

        <button type="submit">
            Daftar Sekarang
        </button>

    </form>

    <div class="link">
        Sudah punya akun?

        <a href="{{ route('login') }}">Login di sini</a>
    </div>

</div>

@endsection