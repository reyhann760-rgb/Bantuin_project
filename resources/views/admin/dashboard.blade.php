{{-- Dashboard Admin: ringkasan platform + tautan ke manajemen kategori. --}}
@extends('layouts.marketplace')

@section('title', 'Dashboard Admin')

@section('content')
@php
    $cards = [
        ['Total Pengguna', $stats['users'], $stats['customers'] . ' customer · ' . $stats['providers'] . ' provider'],
        ['Kategori', $stats['categories'], 'kategori layanan'],
        ['Butuh Jasa', $stats['requests'], $stats['openRequests'] . ' masih terbuka'],
        ['Menyediakan Jasa', $stats['offers'], $stats['activeOffers'] . ' aktif'],
        ['Percakapan', $stats['conversations'], 'total percakapan'],
    ];
@endphp

<section class="border-b border-slate-200 bg-[#f6f7f2]">
    <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-8 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="brand-font text-2xl font-extrabold text-slate-900 md:text-3xl">Dashboard Admin</h1>
                <span class="rounded-full bg-slate-900 px-3 py-0.5 text-xs font-semibold text-white">Panel Admin</span>
            </div>
            <p class="mt-1 text-sm text-slate-500">Halo, {{ auth()->user()->name }}. Ringkasan aktivitas platform Bantuin.</p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row">
            <a href="{{ route('admin.services.index') }}" class="rounded-lg bg-emerald-700 px-5 py-2.5 text-center text-sm font-bold text-white hover:bg-emerald-800">Kelola Jasa</a>
            <a href="{{ route('admin.categories.index') }}" class="rounded-lg bg-orange-500 px-5 py-2.5 text-center text-sm font-bold text-white hover:bg-orange-600">Kelola Kategori</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full rounded-lg border border-red-200 bg-white px-5 py-2.5 text-center text-sm font-semibold text-red-600 hover:bg-red-50">Keluar</button>
            </form>
        </div>
    </div>
</section>

<div class="mx-auto max-w-6xl space-y-8 px-4 py-8">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="status">{{ session('success') }}</div>
    @endif

    {{-- Kartu statistik --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        @foreach ($cards as [$label, $value, $note])
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</p>
                <p class="brand-font mt-2 text-3xl font-extrabold text-slate-900">{{ $value }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $note }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Pengguna terbaru --}}
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="brand-font text-lg font-bold text-slate-900">Pengguna Terbaru</h2>
            <ul class="mt-3 divide-y divide-slate-100">
                @forelse ($latestUsers as $u)
                    <li class="flex items-center justify-between py-2.5 text-sm">
                        <a href="{{ route('profile.show', $u) }}" class="font-medium text-slate-800 hover:text-indigo-600">{{ $u->name }}</a>
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">{{ $u->role === 'provider' ? 'Provider' : 'Customer' }}</span>
                    </li>
                @empty
                    <li class="py-3 text-sm text-slate-500">Belum ada pengguna.</li>
                @endforelse
            </ul>
        </section>

        {{-- Butuh Jasa terbaru --}}
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="brand-font text-lg font-bold text-slate-900">Butuh Jasa Terbaru</h2>
            <ul class="mt-3 divide-y divide-slate-100">
                @forelse ($latestRequests as $r)
                    <li class="py-2.5 text-sm">
                        <a href="{{ route('service-requests.show', $r) }}" class="font-medium text-slate-800 hover:text-indigo-600">{{ $r->title }}</a>
                        <p class="text-xs text-slate-500">{{ $r->customer->name ?? '-' }} · {{ $r->category->name ?? 'Tanpa kategori' }}</p>
                    </li>
                @empty
                    <li class="py-3 text-sm text-slate-500">Belum ada permintaan.</li>
                @endforelse
            </ul>
        </section>

        {{-- Menyediakan Jasa terbaru --}}
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="brand-font text-lg font-bold text-slate-900">Menyediakan Jasa Terbaru</h2>
            <ul class="mt-3 divide-y divide-slate-100">
                @forelse ($latestOffers as $o)
                    <li class="py-2.5 text-sm">
                        <a href="{{ route('service-offers.show', $o) }}" class="font-medium text-slate-800 hover:text-indigo-600">{{ $o->title }}</a>
                        <p class="text-xs text-slate-500">{{ $o->provider->name ?? '-' }} · {{ $o->category->name ?? 'Tanpa kategori' }}</p>
                    </li>
                @empty
                    <li class="py-3 text-sm text-slate-500">Belum ada penawaran.</li>
                @endforelse
            </ul>
        </section>
    </div>
</div>
@endsection
