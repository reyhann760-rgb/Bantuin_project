@extends('layouts.marketplace')
@section('title', 'Beranda')

@section('content')
<section class="overflow-hidden bg-[#f6f7f2]">
    <div class="mx-auto grid max-w-7xl items-center gap-10 px-5 py-12 sm:px-8 md:py-20 lg:grid-cols-[1.05fr_.95fr] lg:gap-16 lg:px-12">
        <div class="max-w-2xl">
            <p class="mb-5 inline-flex items-center gap-2 text-sm font-bold uppercase tracking-[.14em] text-emerald-800">
                <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                Jasa lokal, lebih dekat
            </p>
            <h1 class="brand-font max-w-xl text-4xl font-extrabold leading-[1.08] text-slate-900 sm:text-5xl lg:text-6xl">Ada yang perlu dibantu? <span class="text-emerald-700">Ketemu orangnya.</span></h1>
            <p class="mt-6 max-w-lg text-base leading-7 text-slate-600 sm:text-lg">Temukan bantuan tepercaya di sekitarmu, atau bagikan keahlianmu kepada orang yang sedang membutuhkan.</p>

            <form action="{{ route('butuh.index') }}" method="GET" class="mt-8 flex max-w-xl flex-col gap-2 rounded-lg bg-white p-2 shadow-[0_12px_40px_rgba(27,54,43,.10)] sm:flex-row">
                <label for="service-search" class="sr-only">Cari jasa</label>
                <input id="service-search" type="search" name="q" placeholder="Contoh: service AC, desain logo" class="min-h-12 min-w-0 flex-1 rounded-md border-0 bg-transparent px-4 text-slate-800 outline-none placeholder:text-slate-400 focus:ring-2 focus:ring-emerald-700">
                <button type="submit" class="min-h-12 rounded-md bg-orange-500 px-7 font-bold text-white transition hover:bg-orange-600">Cari jasa</button>
            </form>

            <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm font-semibold">
                <a href="{{ route('butuh.index') }}" class="text-emerald-800 hover:text-emerald-950">Jelajahi permintaan <span aria-hidden="true">&rarr;</span></a>
                <a href="{{ route('sedia.index') }}" class="text-slate-600 hover:text-emerald-800">Temukan penyedia jasa <span aria-hidden="true">&rarr;</span></a>
            </div>
            <div class="mt-10 flex items-center gap-3 border-t border-slate-200 pt-5">
                <div class="flex -space-x-2" aria-hidden="true">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-[#f6f7f2] bg-amber-200 text-xs font-bold text-amber-900">R</span>
                    <span class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-[#f6f7f2] bg-teal-200 text-xs font-bold text-teal-900">D</span>
                    <span class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-[#f6f7f2] bg-rose-200 text-xs font-bold text-rose-900">F</span>
                </div>
                <p class="text-sm text-slate-600"><span class="font-bold text-slate-900">Saling bantu, saling tumbuh.</span><br>Mulai dari kebutuhan sederhana di sekitar kita.</p>
            </div>
        </div>

        <div class="relative min-h-[340px] overflow-hidden rounded-lg bg-emerald-950 sm:min-h-[440px]">
            <img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1400&q=85" alt="Teknisi sedang memperbaiki peralatan di rumah" class="absolute inset-0 h-full w-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-slate-950/5 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 p-6 text-white sm:p-8">
                <p class="text-sm font-semibold text-amber-300">Keahlian yang berguna ada di dekatmu</p>
                <p class="brand-font mt-2 max-w-sm text-2xl font-bold leading-tight sm:text-3xl">Satu koneksi kecil bisa meringankan banyak hal.</p>
            </div>
            <div class="absolute right-4 top-4 rounded-md bg-white px-4 py-3 shadow-lg sm:right-6 sm:top-6">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Mulai dari</p>
                <p class="brand-font mt-1 text-lg font-extrabold text-emerald-800">sekitar kita</p>
            </div>
        </div>
    </div>
</section>

@if ($categories->isNotEmpty())
<section class="border-y border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-5 py-8 sm:px-8 lg:px-12">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[.12em] text-orange-600">Cari sesuai kebutuhan</p>
                <h2 class="brand-font mt-2 text-2xl font-extrabold text-slate-900">Jelajahi kategori</h2>
            </div>
            <a href="{{ route('sedia.index') }}" class="text-sm font-bold text-emerald-800 hover:underline">Semua jasa <span aria-hidden="true">&rarr;</span></a>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            @foreach ($categories as $cat)
                <a href="{{ route('sedia.index', ['category' => $cat->slug ?: $cat->id]) }}" class="group flex min-h-20 items-center justify-between gap-2 rounded-md border border-slate-200 bg-[#fbfcf9] px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-emerald-700 hover:bg-emerald-50 hover:text-emerald-900">
                    <span>{{ $cat->name }}</span><span class="text-lg text-orange-500 transition group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="bg-[#eaf1eb]">
    <div class="mx-auto grid max-w-7xl gap-8 px-5 py-10 sm:px-8 md:grid-cols-[.8fr_2.2fr] md:items-center lg:px-12">
        <div>
            <p class="text-sm font-bold uppercase tracking-[.12em] text-emerald-800">Sesederhana itu</p>
            <h2 class="brand-font mt-2 text-2xl font-extrabold text-slate-900">Cara kerja Bantuin</h2>
            <p class="mt-3 text-sm leading-6 text-slate-600">Dari menemukan kebutuhan sampai sepakat, semua dimulai dari satu langkah.</p>
        </div>
        <div class="grid gap-6 sm:grid-cols-3">
            @foreach ([['01', 'Buat akun', 'Lengkapi profil agar orang tahu siapa kamu.'], ['02', 'Pasang kebutuhan', 'Ceritakan jasa yang dicari atau keahlianmu.'], ['03', 'Hubungi dan sepakati', 'Lanjutkan obrolan untuk menentukan detail.']] as [$no, $judul, $desc])
                <div class="border-t-2 border-emerald-700 pt-4">
                    <p class="brand-font text-sm font-extrabold text-orange-600">{{ $no }}</p>
                    <h3 class="mt-2 font-bold text-slate-900">{{ $judul }}</h3>
                    <p class="mt-1 text-sm leading-6 text-slate-600">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Permintaan Jasa Terbaru (Database Nyata) --}}
<section class="mx-auto max-w-7xl px-5 py-12 sm:px-8 lg:px-12">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm font-bold uppercase tracking-[.12em] text-orange-600">Peluang untuk membantu</p>
            <h2 class="brand-font mt-2 text-2xl font-extrabold text-slate-900">Permintaan jasa terbaru</h2>
        </div>
        <a href="{{ route('butuh.index') }}" class="text-sm font-bold text-emerald-800 hover:underline">Lihat semua <span aria-hidden="true">&rarr;</span></a>
    </div>
    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($recentRequests as $item)
            @php
                $rupiah = fn ($v) => 'Rp ' . number_format($v, 0, ',', '.');
                if ($item->budget_min !== null && $item->budget_max !== null && $item->budget_min != $item->budget_max) {
                    $price = $rupiah($item->budget_min) . ' – ' . $rupiah($item->budget_max);
                } elseif ($item->budget_max !== null || $item->budget_min !== null) {
                    $price = $rupiah($item->budget_max ?? $item->budget_min);
                } else {
                    $price = 'Terbuka';
                }
            @endphp
            <a href="{{ route('service-requests.show', $item) }}" class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-between gap-2">
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                        {{ $item->category?->name ?? 'Butuh Jasa' }}
                    </span>
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-700">Terbuka</span>
                </div>
                <h3 class="mt-3 text-base font-semibold text-slate-900 group-hover:text-indigo-600">{{ $item->title }}</h3>
                <p class="mt-2 line-clamp-2 text-sm text-slate-500">{{ $item->description }}</p>
                <div class="mt-auto flex items-end justify-between pt-4">
                    <div>
                        <p class="text-xs text-slate-400">Anggaran</p>
                        <p class="font-bold text-slate-900">{{ $price }}</p>
                    </div>
                    <p class="text-xs text-slate-500">{{ $item->location ?? 'Online' }} &middot; {{ $item->customer?->name ?? 'Pengguna' }}</p>
                </div>
            </a>
        @empty
            <p class="text-sm text-slate-600 col-span-3 py-6 text-center">Belum ada permintaan jasa terbaru yang terbuka.</p>
        @endforelse
    </div>
</section>

{{-- Penyedia Jasa Terbaru (Database Nyata) --}}
<section class="mx-auto max-w-7xl px-5 pb-14 sm:px-8 lg:px-12">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm font-bold uppercase tracking-[.12em] text-orange-600">Keahlian untuk dibagikan</p>
            <h2 class="brand-font mt-2 text-2xl font-extrabold text-slate-900">Penyedia jasa terbaru</h2>
        </div>
        <a href="{{ route('sedia.index') }}" class="text-sm font-bold text-emerald-800 hover:underline">Lihat semua <span aria-hidden="true">&rarr;</span></a>
    </div>
    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($recentOffers as $item)
            @php
                $price = $item->price !== null ? 'Rp ' . number_format($item->price, 0, ',', '.') : 'Disesuaikan';
            @endphp
            <a href="{{ route('service-offers.show', $item) }}" class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-between gap-2">
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        {{ $item->category?->name ?? 'Menyediakan Jasa' }}
                    </span>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">Tersedia</span>
                </div>
                <h3 class="mt-3 text-base font-semibold text-slate-900 group-hover:text-emerald-700">{{ $item->title }}</h3>
                <p class="mt-2 line-clamp-2 text-sm text-slate-500">{{ $item->description }}</p>
                <div class="mt-auto flex items-end justify-between pt-4">
                    <div>
                        <p class="text-xs text-slate-400">Tarif</p>
                        <p class="font-bold text-slate-900">{{ $price }}</p>
                    </div>
                    <p class="text-xs text-slate-500">{{ $item->location ?? 'Online' }} &middot; {{ $item->provider?->name ?? 'Penyedia' }}</p>
                </div>
            </a>
        @empty
            <p class="text-sm text-slate-600 col-span-3 py-6 text-center">Belum ada penyedia jasa aktif terbaru.</p>
        @endforelse
    </div>
</section>
@endsection
