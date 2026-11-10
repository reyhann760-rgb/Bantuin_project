@extends('layouts.app')

@section('title', 'Dashboard - Bantuin')

@section('content')

<div class="card">

    <h1>Selamat Datang, {{ auth()->user()->name }}!</h1>

    <p>
        Kamu berhasil login ke aplikasi Bantuin.
    </p>

    <hr>

    <h3>Informasi Akun</h3>

    <p>
        <strong>Nama:</strong>
        {{ auth()->user()->name }}
    </p>

    <p>
        <strong>Email:</strong>
        {{ auth()->user()->email }}
    </p>

    <p>
        <strong>Nomor Telepon:</strong>
        {{ auth()->user()->phone ?? 'Belum diisi' }}
    </p>

    <p>
        <strong>Role:</strong>

        @if(auth()->user()->role === 'customer')
            Customer
        @elseif(auth()->user()->role === 'provider')
            Penyedia Jasa
        @endif
    </p>

    <hr>

    @if(auth()->user()->role === 'customer')

        <h3>Dashboard Customer</h3>

        <p>
            Kamu dapat mencari jasa dan membuat kebutuhan jasa.
        </p>

    @elseif(auth()->user()->role === 'provider')

        <h3>Dashboard Penyedia Jasa</h3>

        <p>
            Kamu dapat menawarkan jasa dan menerima permintaan layanan.
        </p>

    @endif

</div>

@endsection