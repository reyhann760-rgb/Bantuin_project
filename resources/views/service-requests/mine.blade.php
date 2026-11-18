{{-- Frontend 2: daftar permintaan milik customer. Nama route & variabel tidak diubah. --}}
@extends('layouts.marketplace')

@section('title', 'Permintaan Saya')

@section('content')
@php
    $statusMap = [
        'open'        => ['Open', 'bg-emerald-100 text-emerald-700'],
        'in_progress' => ['Sedang dikerjakan', 'bg-amber-100 text-amber-700'],
        'completed'   => ['Selesai', 'bg-indigo-100 text-indigo-700'],
        'cancelled'   => ['Dibatalkan', 'bg-slate-200 text-slate-600'],
    ];
    $fmt = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
@endphp

<section class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-4 py-8">
        <div>
            <h1 class="text-2xl font-bold md:text-3xl">Permintaan Saya</h1>
            <p class="mt-1 text-slate-500">Daftar permintaan jasa yang kamu buat.</p>
        </div>
        <a href="{{ route('service-requests.create') }}" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">+ Buat Permintaan</a>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-8">
    <x-flash />

    @forelse ($requests as $item)
        @php [$label, $class] = $statusMap[$item->status] ?? [$item->status, 'bg-slate-100 text-slate-600']; @endphp
        @if ($loop->first)<div class="space-y-4">@endif

        <article class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $class }}">{{ $label }}</span>
                    <span class="text-xs text-slate-400">Dibuat {{ $item->created_at->locale('id')->translatedFormat('d F Y') }}</span>
                </div>
                <h2 class="mt-2 truncate text-base font-semibold text-slate-900">
                    <a href="{{ route('service-requests.show', $item) }}" class="hover:text-indigo-600">{{ $item->title }}</a>
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    @if ($item->budget_min !== null || $item->budget_max !== null)
                        {{ $fmt($item->budget_min ?? 0) }} &ndash; {{ $fmt($item->budget_max ?? 0) }}
                    @else
                        Budget belum ditentukan
                    @endif
                    @if ($item->location) &middot; {{ $item->location }} @endif
                </p>
            </div>

            <div class="flex shrink-0 gap-2">
                <a href="{{ route('service-requests.show', $item) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Lihat</a>
                <a href="{{ route('service-requests.edit', $item) }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Edit</a>
            </div>
        </article>

        @if ($loop->last)</div>@endif
    @empty
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-16 text-center">
            <p class="font-semibold">Kamu belum membuat permintaan</p>
            <p class="mt-1 text-sm text-slate-500">Ceritakan jasa yang kamu butuhkan agar penyedia jasa bisa menghubungimu.</p>
            <a href="{{ route('service-requests.create') }}" class="mt-4 inline-block rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">+ Buat Permintaan</a>
        </div>
    @endforelse

    @if ($requests->hasPages())
        <div class="mt-6 flex justify-between text-sm font-medium">
            @if (! $requests->onFirstPage())
                <a href="{{ $requests->previousPageUrl() }}" class="text-indigo-600 hover:underline">&laquo; Sebelumnya</a>
            @else <span></span> @endif
            @if ($requests->hasMorePages())
                <a href="{{ $requests->nextPageUrl() }}" class="text-indigo-600 hover:underline">Berikutnya &raquo;</a>
            @endif
        </div>
    @endif
</section>
@endsection
