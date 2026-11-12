{{-- PLACEHOLDER dari Backend 2: tampilan polos. Frontend silakan ganti isi file ini (nama file, variabel, nama field, dan nama route jangan diubah). --}}
@extends('layouts.app')

@section('title', 'Permintaan Saya - Bantuin')

@section('content')

<div class="card">

    <h1>Permintaan Saya</h1>

    <p>Daftar permintaan jasa yang kamu buat.</p>

    <a class="button" href="{{ route('service-requests.create') }}">+ Buat Permintaan</a>

    <hr>

    @forelse($requests as $item)
        <div class="form-group">
            <strong><a href="{{ route('service-requests.show', $item) }}">{{ $item->title }}</a></strong><br>
            Status: {{ $item->status }} |
            <a href="{{ route('service-requests.edit', $item) }}">Edit</a>
        </div>
    @empty
        <p>Kamu belum membuat permintaan.</p>
    @endforelse

    @if($requests->hasPages())
        <div class="link">
            @if(!$requests->onFirstPage())
                <a href="{{ $requests->previousPageUrl() }}">&laquo; Sebelumnya</a>
            @endif
            @if($requests->hasMorePages())
                <a href="{{ $requests->nextPageUrl() }}">Berikutnya &raquo;</a>
            @endif
        </div>
    @endif

</div>

@endsection