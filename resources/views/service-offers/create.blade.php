{{-- PLACEHOLDER dari Backend 2: tampilan polos. Frontend silakan ganti isi file ini (nama file, variabel, nama field, dan nama route jangan diubah). --}}
@extends('layouts.app')

@section('title', 'Buat Menyediakan Jasa - Bantuin')

@section('content')

<div class="card">

    <h1>Buat Menyediakan Jasa</h1>

    <p>Tawarkan jasa yang bisa kamu berikan.</p>

    <form action="{{ route('service-offers.store') }}" method="POST">

        @csrf

        @include('service-offers._form')

        <button type="submit">Simpan</button>

    </form>

    <div class="link">
        <a href="{{ route('service-offers.mine') }}">Kembali</a>
    </div>

</div>

@endsection