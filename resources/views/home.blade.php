@extends('layouts.marketplace')
@section('title', 'Beranda')

@section('content')
{{-- Hero --}}
<section class="bg-gradient-to-br from-indigo-600 to-violet-600 text-white">
    <div class="mx-auto max-w-6xl px-4 py-16 text-center md:py-24">
        <h1 class="text-3xl font-extrabold leading-tight md:text-5xl">Butuh bantuan? Bantuin aja.</h1>
        <p class="mx-auto mt-4 max-w-2xl text-indigo-100">Temukan penyedia jasa terpercaya, atau tawarkan keahlianmu kepada orang yang membutuhkan.</p>

        <form action="{{ route('butuh.index') }}" method="GET" class="mx-auto mt-8 flex max-w-xl flex-col gap-2 sm:flex-row">
            <input type="text" name="q" placeholder="Cari jasa, misal: service AC"
       class="flex-1 rounded-xl bg-white px-4 py-3 text-slate-800 placeholder-slate-400 outline-none focus:ring-2 focus:ring-amber-300">
            <button class="rounded-xl bg-amber-400 px-6 py-3 font-semibold text-slate-900 hover:bg-amber-300">Cari</button>
        </form>

        <div class="mt-6 flex flex-wrap justify-center gap-3 text-sm">
            <a href="{{ route('butuh.index') }}" class="rounded-full border border-white/40 px-4 py-2 hover:bg-white/10">Lihat yang Butuh Jasa</a>
            <a href="{{ route('sedia.index') }}" class="rounded-full border border-white/40 px-4 py-2 hover:bg-white/10">Lihat yang Menyediakan Jasa</a>
        </div>
    </div>
</section>

{{-- Kategori --}}
<section class="mx-auto max-w-6xl px-4 py-12">
    <h2 class="text-xl font-bold">Kategori populer</h2>
    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
        @foreach ($kategori as $k)
            <a href="{{ route('sedia.index', ['kategori' => $k]) }}"
               class="rounded-xl border border-slate-200 bg-white px-3 py-4 text-center text-sm font-medium text-slate-700 hover:border-indigo-300 hover:text-indigo-600">{{ $k }}</a>
        @endforeach
    </div>
</section>

{{-- Cara kerja --}}
<section class="bg-white py-12">
    <div class="mx-auto max-w-6xl px-4">
        <h2 class="text-center text-xl font-bold">Cara kerja Bantuin</h2>
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            @foreach ([['1', 'Buat akun', 'Daftar gratis lalu lengkapi profilmu.'], ['2', 'Pasang permintaan atau penawaran', 'Posting kebutuhan jasa atau jasa yang kamu sediakan.'], ['3', 'Chat dan sepakati', 'Hubungi pengguna lain lewat fitur chat.']] as [$no, $judul, $desc])
                <div class="rounded-2xl bg-slate-50 p-6 text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 font-bold text-white">{{ $no }}</div>
                    <h3 class="mt-3 font-semibold">{{ $judul }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Terbaru: Butuh Jasa --}}
<section class="mx-auto max-w-6xl px-4 py-12">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold">Butuh jasa terbaru</h2>
        <a href="{{ route('butuh.index') }}" class="text-sm font-medium text-indigo-600 hover:underline">Lihat semua</a>
    </div>
    <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($butuhTerbaru as $item)
            <x-service-card :item="$item" type="butuh" />
        @endforeach
    </div>
</section>

{{-- Terbaru: Menyediakan Jasa --}}
<section class="mx-auto max-w-6xl px-4 pb-12">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold">Penyedia jasa terbaru</h2>
        <a href="{{ route('sedia.index') }}" class="text-sm font-medium text-indigo-600 hover:underline">Lihat semua</a>
    </div>
    <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($sediaTerbaru as $item)
            <x-service-card :item="$item" type="sedia" />
        @endforeach
    </div>
</section>
@endsection
