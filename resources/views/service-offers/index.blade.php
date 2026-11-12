{{-- PLACEHOLDER dari Backend 2: tampilan polos. Frontend silakan ganti isi file ini (nama file, variabel, nama field, dan nama route jangan diubah). --}}
@extends('layouts.app')

@section('title', 'Daftar Menyediakan Jasa - Bantuin')

@section('content')

<div class="card">

    <h1>Daftar Menyediakan Jasa</h1>

    <p>Penawaran jasa dari penyedia jasa yang aktif.</p>

    <form action="{{ route('service-offers.index') }}" method="GET">

        <div class="form-group">
            <label>Cari</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul atau deskripsi">
        </div>

        <div class="form-group">
            <label>Mode Kerja</label>
            <select name="work_mode">
                <option value="">Semua</option>
                @foreach(['online', 'onsite', 'hybrid'] as $mode)
                    <option value="{{ $mode }}" @selected(request('work_mode') === $mode)>{{ ucfirst($mode) }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit">Cari</button>

    </form>

    <hr>

    @forelse($offers as $item)
        <div class="form-group">
            <strong><a href="{{ route('service-offers.show', $item) }}">{{ $item->title }}</a></strong><br>
            Oleh {{ $item->provider->name }} | {{ $item->work_mode ?? '-' }} | {{ $item->status }}
        </div>
    @empty
        <p>Belum ada penawaran jasa.</p>
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