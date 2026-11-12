{{-- PLACEHOLDER dari Backend 2: tampilan polos. Frontend silakan ganti isi file ini (nama file, variabel, nama field, dan nama route jangan diubah). --}}
@extends('layouts.app')

@section('title', 'Penawaran Saya - Bantuin')

@section('content')

<div class="card">

    <h1>Penawaran Saya</h1>

    <p>Daftar penawaran jasa yang kamu buat.</p>

    <a class="button" href="{{ route('service-offers.create') }}">+ Buat Penawaran</a>

    <hr>

    @forelse($offers as $item)
        <div class="form-group">
            <strong><a href="{{ route('service-offers.show', $item) }}">{{ $item->title }}</a></strong><br>
            Status: {{ $item->status }} |
            <a href="{{ route('service-offers.edit', $item) }}">Edit</a>
        </div>
    @empty
        <p>Kamu belum membuat penawaran.</p>
    @endforelse

    @if($offers->hasPages())
        <div class="link">
            @if(!$offers->onFirstPage())
                <a href="{{ $offers->previousPageUrl() }}">&laquo; Sebelumnya</a>
            @endif
            @if($offers->hasMorePages())
                <a href="{{ $offers->nextPageUrl() }}">Berikutnya &raquo;</a>
            @endif
        </div>
    @endif

</div>

@endsection