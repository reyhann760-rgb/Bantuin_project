{{-- PLACEHOLDER dari Backend 2: tampilan polos. Frontend silakan ganti isi file ini (nama file, variabel, nama field, dan nama route jangan diubah). --}}
@extends('layouts.app')

@section('title', 'Buat Butuh Jasa - Bantuin')

@section('content')

<div class="card">

    <h1>Buat Butuh Jasa</h1>

    <p>Ceritakan jasa yang kamu butuhkan.</p>

    <form action="{{ route('service-requests.store') }}" method="POST">

        @csrf

        @include('service-requests._form')

        <button type="submit">Simpan</button>

    </form>

    <div class="link">
        <a href="{{ route('service-requests.mine') }}">Kembali</a>
    </div>

</div>

@endsection