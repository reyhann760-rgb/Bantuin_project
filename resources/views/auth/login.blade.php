@extends('layouts.app')

@section('title', 'Login - Bantuin')

@section('content')

<div class="card">

    <h1>Login Bantuin</h1>

    <p>Masuk ke akun Bantuin kamu.</p>

    <form action="{{ route('login.store') }}" method="POST">

        @csrf

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
            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Masukkan password"
                required
            >
        </div>

        <div class="form-group">
            <label>
                <input
                    type="checkbox"
                    name="remember"
                    value="1"
                    style="width: auto;"
                >
                Ingat Saya
            </label>
        </div>

        <button type="submit">
            Login
        </button>

    </form>

    <div class="link">
        Belum punya akun?

        <a href="{{ route('register') }}">Daftar sekarang</a>
    </div>

</div>

@endsection