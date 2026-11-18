{{-- Frontend 2: detail Menyediakan Jasa. Nama route & variabel tidak diubah. --}}
@extends('layouts.marketplace')

@section('title', $serviceOffer->title)

@section('content')
@php
    $statusMap = [
        'active'   => ['Aktif', 'bg-emerald-100 text-emerald-700'],
        'inactive' => ['Nonaktif', 'bg-slate-200 text-slate-600'],
    ];
    [$statusLabel, $statusClass] = $statusMap[$serviceOffer->status] ?? [$serviceOffer->status, 'bg-slate-100 text-slate-600'];
    $modeMap = ['online' => 'Online', 'onsite' => 'Onsite (tatap muka)', 'hybrid' => 'Hybrid'];
    $typeMap = ['fixed' => 'Harga tetap', 'hourly' => 'Per jam', 'negotiable' => 'Bisa nego'];

    $harga = $serviceOffer->price !== null
        ? 'Rp ' . number_format($serviceOffer->price, 0, ',', '.')
        : 'Belum ditentukan';
    $isOwner = auth()->id() === $serviceOffer->provider_id;
    $portfolio = $serviceOffer->portfolio_url;
    $portfolioOk = $portfolio && (str_starts_with($portfolio, 'http://') || str_starts_with($portfolio, 'https://'));
@endphp

<div class="mx-auto max-w-6xl px-4 py-8">
    <a href="{{ $isOwner ? route('service-offers.mine') : route('service-offers.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; {{ $isOwner ? 'Kembali ke penawaran saya' : 'Kembali ke daftar' }}</a>

    <div class="mt-4">@foreach (['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-red-200 bg-red-50 text-red-700', 'info' => 'border-indigo-200 bg-indigo-50 text-indigo-800'] as $flashKey => $flashClass)
@if (session($flashKey))
<div class="mb-4 rounded-xl border p-4 text-sm {{ $flashClass }}" role="{{ $flashKey === 'error' ? 'alert' : 'status' }}">{{ session($flashKey) }}</div>
@endif
@endforeach</div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Detail utama --}}
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusClass }}">{{ $statusLabel }}</span>
            <h1 class="mt-3 text-2xl font-bold md:text-3xl">{{ $serviceOffer->title }}</h1>
            <p class="mt-1 text-sm text-slate-400">Diposting {{ $serviceOffer->created_at->locale('id')->translatedFormat('d F Y') }}</p>

            <h2 class="mt-6 font-semibold">Deskripsi</h2>
            <p class="mt-2 whitespace-pre-line leading-relaxed text-slate-600">{{ $serviceOffer->description }}</p>

            <dl class="mt-6 grid grid-cols-1 gap-4 rounded-xl bg-slate-50 p-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-slate-400">Harga</dt>
                    <dd class="font-semibold">{{ $harga }}@if ($serviceOffer->price_type) <span class="font-normal text-slate-500">({{ $typeMap[$serviceOffer->price_type] ?? $serviceOffer->price_type }})</span>@endif</dd>
                </div>
                <div><dt class="text-slate-400">Mode kerja</dt><dd class="font-semibold">{{ $modeMap[$serviceOffer->work_mode] ?? '-' }}</dd></div>
                <div><dt class="text-slate-400">Lokasi</dt><dd class="font-semibold">{{ $serviceOffer->location ?? '-' }}</dd></div>
                <div>
                    <dt class="text-slate-400">Portofolio</dt>
                    <dd class="break-all font-semibold">
                        @if ($portfolioOk)
                            <a href="{{ $portfolio }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline">{{ $portfolio }}</a>
                        @else
                            -
                        @endif
                    </dd>
                </div>
            </dl>
        </article>

        {{-- Sidebar --}}
        <aside class="h-fit space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-700">
                    {{ strtoupper(mb_substr($serviceOffer->provider->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-xs text-slate-400">Ditawarkan oleh</p>
                    <p class="font-semibold">{{ $serviceOffer->provider->name }}</p>
                </div>
            </div>

            @if ($serviceOffer->provider->bio)
                <p class="text-sm text-slate-500">{{ $serviceOffer->provider->bio }}</p>
            @endif

            @if ($isOwner)
                <a href="{{ route('service-offers.edit', $serviceOffer) }}"
                   class="block rounded-lg bg-emerald-600 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-emerald-700">Edit Penawaran</a>
            @elseif (auth()->check() && auth()->user()->role === 'customer' && $serviceOffer->status === 'active')
                <form action="{{ route('chat.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="service_offer_id" value="{{ $serviceOffer->id }}">
                    <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Mulai Chat / Pesan Jasa</button>
                </form>
            @endif
        </aside>

    </div>
</div>
@endsection