{{-- PLACEHOLDER dari Backend 2: tampilan polos. Frontend silakan ganti isi file ini (nama file, variabel, nama field, dan nama route jangan diubah). --}}
@extends('layouts.app')

@section('title', $serviceOffer->title . ' - Bantuin')

@section('content')

<div class="card">

    <h1>{{ $serviceOffer->title }}</h1>

    <p>Oleh <strong>{{ $serviceOffer->provider->name }}</strong> | Status: {{ $serviceOffer->status }}</p>

    <p>{{ $serviceOffer->description }}</p>

    <hr>

    <p><strong>Harga:</strong> Rp {{ number_format($serviceOffer->price ?? 0, 0, ',', '.') }} ({{ $serviceOffer->price_type ?? '-' }})</p>
    <p><strong>Mode Kerja:</strong> {{ $serviceOffer->work_mode ?? '-' }}</p>
    <p><strong>Lokasi:</strong> {{ $serviceOffer->location ?? '-' }}</p>
    <p><strong>Portofolio:</strong> {{ $serviceOffer->portfolio_url ?? '-' }}</p>

    @if(auth()->id() === $serviceOffer->provider_id)
        <a class="button" href="{{ route('service-offers.edit', $serviceOffer) }}">Edit</a>
    @endif

    <div class="link">
        <a href="{{ route('service-offers.index') }}">Kembali ke daftar</a>
    </div>

</div>

@endsection