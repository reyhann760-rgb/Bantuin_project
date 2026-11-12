{{-- PLACEHOLDER dari Backend 2: tampilan polos. Frontend silakan ganti isi file ini (nama file, variabel, nama field, dan nama route jangan diubah). --}}
@extends('layouts.app')

@section('title', $serviceRequest->title . ' - Bantuin')

@section('content')

<div class="card">

    <h1>{{ $serviceRequest->title }}</h1>

    <p>Oleh <strong>{{ $serviceRequest->customer->name }}</strong> | Status: {{ $serviceRequest->status }}</p>

    <p>{{ $serviceRequest->description }}</p>

    <hr>

    <p><strong>Budget:</strong> Rp {{ number_format($serviceRequest->budget_min ?? 0, 0, ',', '.') }} - Rp {{ number_format($serviceRequest->budget_max ?? 0, 0, ',', '.') }}</p>
    <p><strong>Mode Kerja:</strong> {{ $serviceRequest->work_mode ?? '-' }}</p>
    <p><strong>Lokasi:</strong> {{ $serviceRequest->location ?? '-' }}</p>
    <p><strong>Deadline:</strong> {{ $serviceRequest->deadline ?? '-' }}</p>

    @if(auth()->id() === $serviceRequest->customer_id)
        <a class="button" href="{{ route('service-requests.edit', $serviceRequest) }}">Edit</a>
    @endif

    <div class="link">
        <a href="{{ route('service-requests.index') }}">Kembali ke daftar</a>
    </div>

</div>

@endsection