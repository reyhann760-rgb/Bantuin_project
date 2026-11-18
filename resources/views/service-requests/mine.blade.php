@extends('layouts.marketplace')

@section('title', 'Permintaan Saya')

@section('content')
@php
    $statusMap = [
        'open'        => ['Terbuka', 'bg-emerald-100 text-emerald-700'],
        'in_progress' => ['Berjalan', 'bg-amber-100 text-amber-700'],
        'completed'   => ['Selesai', 'bg-slate-200 text-slate-600'],
        'cancelled'   => ['Dibatalkan', 'bg-red-100 text-red-700'],
    ];
    $modeMap = ['online' => 'Online', 'onsite' => 'Onsite', 'hybrid' => 'Hybrid'];

    $rupiah = fn ($v) => 'Rp ' . number_format($v, 0, ',', '.');
@endphp

<div class="mx-auto max-w-6xl px-4 py-8">
    <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke dashboard</a>

    {{-- Header --}}
    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold md:text-3xl">Permintaan Saya</h1>
            <p class="mt-1 text-slate-500">Daftar permintaan jasa yang kamu buat.</p>
        </div>
        <a href="{{ route('service-requests.create') }}"
           class="rounded-lg bg-indigo-600 px-5 py-2.5 text-center text-sm font-semibold text-white hover:bg-indigo-700">+ Buat Butuh Jasa</a>
    </div>

    <div class="mt-4">@foreach (['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-red-200 bg-red-50 text-red-700', 'info' => 'border-indigo-200 bg-indigo-50 text-indigo-800'] as $flashKey => $flashClass)
@if (session($flashKey))
<div class="mb-4 rounded-xl border p-4 text-sm {{ $flashClass }}" role="{{ $flashKey === 'error' ? 'alert' : 'status' }}">{{ session($flashKey) }}</div>
@endif
@endforeach</div>

    {{-- Daftar --}}
    <div class="mt-6 space-y-4">
        @forelse ($requests as $item)
            @php
                [$statusLabel, $statusClass] = $statusMap[$item->status] ?? [$item->status, 'bg-slate-100 text-slate-600'];

                if ($item->budget_min !== null && $item->budget_max !== null && $item->budget_min != $item->budget_max) {
                    $budget = $rupiah($item->budget_min) . ' – ' . $rupiah($item->budget_max);
                } elseif ($item->budget_max !== null || $item->budget_min !== null) {
                    $budget = $rupiah($item->budget_max ?? $item->budget_min);
                } else {
                    $budget = 'Belum ditentukan';
                }
            @endphp

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusClass }}">{{ $statusLabel }}</span>
                            <span class="text-xs text-slate-400">Diposting {{ $item->created_at->locale('id')->translatedFormat('d F Y') }}</span>
                        </div>

                        <h2 class="mt-2 text-lg font-semibold text-slate-900">
                            <a href="{{ route('service-requests.show', $item) }}" class="hover:text-indigo-600">{{ $item->title }}</a>
                        </h2>
                        <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $item->description }}</p>

                        <dl class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-3 text-sm sm:grid-cols-4">
                            <div>
                                <dt class="text-xs text-slate-400">Anggaran</dt>
                                <dd class="font-semibold">{{ $budget }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-400">Mode kerja</dt>
                                <dd class="font-semibold">{{ $modeMap[$item->work_mode] ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-400">Lokasi</dt>
                                <dd class="font-semibold">{{ $item->location ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-400">Batas waktu</dt>
                                <dd class="font-semibold">{{ $item->deadline ? \Illuminate\Support\Carbon::parse($item->deadline)->locale('id')->translatedFormat('d M Y') : '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Aksi --}}
                    <div class="flex shrink-0 flex-row gap-2 md:flex-col">
                        <a href="{{ route('service-requests.show', $item) }}"
                           class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">Lihat</a>
                        <a href="{{ route('service-requests.edit', $item) }}"
                           class="flex-1 rounded-lg bg-emerald-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-emerald-700">Edit</a>
                        <form action="{{ route('service-requests.destroy', $item) }}" method="POST" class="flex-1"
                              onsubmit="return confirm('Hapus permintaan ini? Tindakan ini tidak bisa dibatalkan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Hapus</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                <p class="font-semibold text-slate-700">Kamu belum membuat permintaan.</p>
                <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">Ceritakan jasa yang kamu butuhkan, penyedia jasa akan menghubungimu lewat chat.</p>
                <a href="{{ route('service-requests.create') }}"
                   class="mt-5 inline-block rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">+ Buat Butuh Jasa</a>
            </div>
        @endforelse
    </div>

    @if ($requests->hasPages())
    <nav class="mt-6 flex items-center justify-between" aria-label="Navigasi halaman">
        @if ($requests->onFirstPage())
            <span class="rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-300">&laquo; Sebelumnya</span>
        @else
            <a href="{{ $requests->previousPageUrl() }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">&laquo; Sebelumnya</a>
        @endif

        <span class="text-sm text-slate-500">Halaman {{ $requests->currentPage() }} dari {{ $requests->lastPage() }}</span>

        @if ($requests->hasMorePages())
            <a href="{{ $requests->nextPageUrl() }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Berikutnya &raquo;</a>
        @else
            <span class="rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-300">Berikutnya &raquo;</span>
        @endif
    </nav>
    @endif
</div>
@endsection
