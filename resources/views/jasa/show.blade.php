@extends('layouts.marketplace')
@section('title', $item->judul)

@section('content')
@php
    $isButuh = $type === 'butuh';
    $indexRoute = $isButuh ? 'butuh.index' : 'sedia.index';
@endphp
<div class="mx-auto max-w-6xl px-4 py-8">
    <a href="{{ route($indexRoute) }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke daftar</a>

    <div class="mt-4 grid gap-6 lg:grid-cols-3">
        {{-- Detail utama --}}
        <article class="rounded-2xl border border-slate-200 bg-white p-6 lg:col-span-2">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-600">{{ $item->kategori }}</span>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $item->status }}</span>
            </div>
            <h1 class="mt-3 text-2xl font-bold md:text-3xl">{{ $item->judul }}</h1>
            <p class="mt-1 text-sm text-slate-400">Diposting {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</p>

            <h2 class="mt-6 font-semibold">Deskripsi</h2>
            <p class="mt-2 leading-relaxed text-slate-600">{{ $item->deskripsi }}</p>

            <dl class="mt-6 grid grid-cols-2 gap-4 rounded-xl bg-slate-50 p-4 text-sm">
                <div><dt class="text-slate-400">{{ $isButuh ? 'Anggaran' : 'Tarif mulai dari' }}</dt><dd class="font-semibold">Rp {{ number_format($item->harga, 0, ',', '.') }}</dd></div>
                <div><dt class="text-slate-400">Lokasi</dt><dd class="font-semibold">{{ $item->lokasi }}</dd></div>
            </dl>
        </article>

        {{-- Sidebar pengguna --}}
        <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-xs text-slate-400">{{ $isButuh ? 'Diminta oleh' : 'Ditawarkan oleh' }}</p>
            <div class="mt-3 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-600 font-bold text-white">{{ strtoupper(substr($item->pengguna, 0, 1)) }}</div>
                <p class="font-semibold">{{ $item->pengguna }}</p>
            </div>
            {{-- Tombol ini akan diarahkan ke halaman chat (Frontend 2) --}}
            <a href="#" class="mt-5 block rounded-lg bg-indigo-600 py-3 text-center text-sm font-semibold text-white hover:bg-indigo-700">
                {{ $isButuh ? 'Tawarkan Jasa / Chat' : 'Pesan / Chat' }}
            </a>
        </aside>
    </div>

    @if ($lainnya->isNotEmpty())
        <h2 class="mt-12 text-xl font-bold">Lainnya untuk kamu</h2>
        <div class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($lainnya as $l)
                <x-service-card :item="$l" :type="$type" />
            @endforeach
        </div>
    @endif
</div>
@endsection
