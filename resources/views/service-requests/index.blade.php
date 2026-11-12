{{-- PLACEHOLDER dari Backend 2: tampilan polos. Frontend silakan ganti isi file ini (nama file, variabel, nama field, dan nama route jangan diubah). --}}
@extends('layouts.app')

@section('title', 'Daftar Butuh Jasa - Bantuin')

@section('content')

<div class="card">

    <h1>Daftar Butuh Jasa</h1>

    <p>Permintaan jasa dari customer yang masih terbuka.</p>

    <form action="{{ route('service-requests.index') }}" method="GET">

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

    @forelse($requests as $item)
        <div class="form-group">
            <strong><a href="{{ route('service-requests.show', $item) }}">{{ $item->title }}</a></strong><br>
            Oleh {{ $item->customer->name }} | {{ $item->work_mode ?? '-' }} | {{ $item->status }}
        </div>
    @empty
        <p>Belum ada permintaan jasa.</p>
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