@extends('layouts.marketplace')

@section('title', $serviceRequest->title)

@section('content')
@php
    $statusMap = [
        'open'        => ['Terbuka', 'bg-emerald-100 text-emerald-700'],
        'in_progress' => ['Sedang dikerjakan', 'bg-amber-100 text-amber-700'],
        'completed'   => ['Selesai', 'bg-slate-200 text-slate-600'],
        'cancelled'   => ['Dibatalkan', 'bg-red-100 text-red-700'],
    ];
    [$statusLabel, $statusClass] = $statusMap[$serviceRequest->status] ?? [$serviceRequest->status, 'bg-slate-100 text-slate-600'];
    $modeMap = ['online' => 'Online', 'onsite' => 'Onsite (tatap muka)', 'hybrid' => 'Hybrid'];

    $rupiah = fn ($v) => 'Rp ' . number_format($v, 0, ',', '.');
    $min = $serviceRequest->budget_min;
    $max = $serviceRequest->budget_max;
    if ($min !== null && $max !== null && $min != $max) {
        $budget = $rupiah($min) . ' – ' . $rupiah($max);
    } elseif ($min !== null || $max !== null) {
        $budget = $rupiah($max ?? $min);
    } else {
        $budget = 'Belum ditentukan';
    }

    $deadline = $serviceRequest->deadline
        ? \Illuminate\Support\Carbon::parse($serviceRequest->deadline)->locale('id')->translatedFormat('d F Y')
        : '-';
    $isOwner = auth()->id() === $serviceRequest->customer_id;
@endphp

<div class="mx-auto max-w-6xl px-4 py-8">
    <a href="{{ $isOwner ? route('service-requests.mine') : route('service-requests.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; {{ $isOwner ? 'Kembali ke permintaan saya' : 'Kembali ke daftar' }}</a>

    <div class="mt-4">@foreach (['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-red-200 bg-red-50 text-red-700', 'info' => 'border-indigo-200 bg-indigo-50 text-indigo-800'] as $flashKey => $flashClass)
@if (session($flashKey))
<div class="mb-4 rounded-xl border p-4 text-sm {{ $flashClass }}" role="{{ $flashKey === 'error' ? 'alert' : 'status' }}">{{ session($flashKey) }}</div>
@endif
@endforeach</div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Detail utama --}}
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusClass }}">{{ $statusLabel }}</span>
            <h1 class="mt-3 text-2xl font-bold md:text-3xl">{{ $serviceRequest->title }}</h1>
            <p class="mt-1 text-sm text-slate-400">Diposting {{ $serviceRequest->created_at->locale('id')->translatedFormat('d F Y') }}</p>

            <h2 class="mt-6 font-semibold">Deskripsi</h2>
            <p class="mt-2 whitespace-pre-line leading-relaxed text-slate-600">{{ $serviceRequest->description }}</p>

            <dl class="mt-6 grid grid-cols-1 gap-4 rounded-xl bg-slate-50 p-4 text-sm sm:grid-cols-2">
                <div><dt class="text-slate-400">Anggaran</dt><dd class="font-semibold">{{ $budget }}</dd></div>
                <div><dt class="text-slate-400">Mode kerja</dt><dd class="font-semibold">{{ $modeMap[$serviceRequest->work_mode] ?? '-' }}</dd></div>
                <div><dt class="text-slate-400">Lokasi</dt><dd class="font-semibold">{{ $serviceRequest->location ?? '-' }}</dd></div>
                <div><dt class="text-slate-400">Deadline</dt><dd class="font-semibold">{{ $deadline }}</dd></div>
            </dl>
        </article>

        {{-- Sidebar --}}
        <aside class="h-fit space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700">
                    {{ strtoupper(mb_substr($serviceRequest->customer->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-xs text-slate-400">Diminta oleh</p>
                    <p class="font-semibold">{{ $serviceRequest->customer->name }}</p>
                </div>
            </div>

            @if ($serviceRequest->customer->bio)
                <p class="text-sm text-slate-500">{{ $serviceRequest->customer->bio }}</p>
            @endif

            @if ($isOwner)
                <a href="{{ route('service-requests.edit', $serviceRequest) }}"
                   class="block rounded-lg bg-indigo-600 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-indigo-700">Edit Permintaan</a>
            @elseif (auth()->check() && auth()->user()->role === 'provider' && $serviceRequest->status === 'open')
                <form action="{{ route('chat.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="service_request_id" value="{{ $serviceRequest->id }}">
                    <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Tawarkan Jasa / Mulai Chat</button>
                </form>
            @endif
        </aside>

    </div>
</div>
@endsection
