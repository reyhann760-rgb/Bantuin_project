{{-- Frontend 2: daftar publik Menyediakan Jasa. Nama route & variabel ($offers) tidak diubah. --}}
@extends('layouts.marketplace')

@section('title', 'Daftar Menyediakan Jasa')

@section('content')
<section class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-6xl px-4 py-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold md:text-3xl">Daftar Menyediakan Jasa</h1>
                <p class="mt-1 text-slate-500">Penawaran jasa dari penyedia jasa yang sedang aktif.</p>
            </div>

            @if (auth()->user()?->role === 'provider')
                <a href="{{ route('service-offers.create') }}" class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">+ Buat Menyediakan Jasa</a>
            @endif
        </div>

        <form action="{{ route('service-offers.index') }}" method="GET" class="mt-5 grid gap-2 sm:grid-cols-[1fr_200px_auto]">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul atau deskripsi..." aria-label="Cari"
                   class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm placeholder-slate-400 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
            <select name="work_mode" aria-label="Mode kerja"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                <option value="">Semua mode kerja</option>
                @foreach (['online' => 'Online', 'onsite' => 'Onsite (tatap muka)', 'hybrid' => 'Hybrid'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('work_mode') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Cari</button>
        </form>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-8">
    @foreach (['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-red-200 bg-red-50 text-red-700', 'info' => 'border-indigo-200 bg-indigo-50 text-indigo-800'] as $flashKey => $flashClass)
    @if (session($flashKey))
    <div class="mb-4 rounded-xl border p-4 text-sm {{ $flashClass }}" role="{{ $flashKey === 'error' ? 'alert' : 'status' }}">{{ session($flashKey) }}</div>
    @endif
    @endforeach

    @if ($offers->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
            <p class="font-semibold text-slate-700">Belum ada penawaran jasa.</p>
            <p class="mt-1 text-sm text-slate-500">
                @if (request()->hasAny(['q', 'work_mode']))
                    Coba ubah kata kunci atau mode kerja.
                @else
                    Data akan muncul di sini setelah ada yang membuatnya.
                @endif
            </p>
            @if (request()->hasAny(['q', 'work_mode']))
                <a href="{{ route('service-offers.index') }}" class="mt-4 inline-block text-sm font-medium text-indigo-600 hover:underline">Reset filter</a>
            @endif
        </div>
    @else
        <p class="mb-4 text-sm text-slate-500">{{ $offers->total() }} hasil ditemukan</p>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($offers as $item)
                @php
                    $person = $item->provider;
                    $modeMap = ['online' => 'Online', 'onsite' => 'Onsite', 'hybrid' => 'Hybrid'];
                    $typeMap = ['fixed' => 'harga tetap', 'hourly' => 'per jam', 'negotiable' => 'bisa nego'];
                    $price = $item->price !== null ? 'Rp ' . number_format($item->price, 0, ',', '.') : 'Belum ditentukan';
                    $priceNote = $typeMap[$item->price_type] ?? null;
                @endphp
                <a href="{{ route('service-offers.show', $item) }}" class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-center justify-between gap-2">
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">Menyediakan Jasa</span>
                        @if ($item->work_mode)
                            <span class="text-xs text-slate-400">{{ $modeMap[$item->work_mode] ?? $item->work_mode }}</span>
                        @endif
                    </div>

                    <h3 class="mt-3 text-base font-semibold text-slate-900 group-hover:text-indigo-600">{{ $item->title }}</h3>
                    <p class="mt-2 line-clamp-2 text-sm text-slate-500">{{ $item->description }}</p>

                    <div class="mt-auto flex items-end justify-between gap-3 pt-4">
                        <div>
                            <p class="text-xs text-slate-400">Harga</p>
                            <p class="font-bold text-slate-900">{{ $price }}@if ($priceNote) <span class="text-xs font-normal text-slate-500">({{ $priceNote }})</span>@endif</p>
                        </div>
                        @if ($item->location)
                            <p class="text-right text-xs text-slate-500">{{ $item->location }}</p>
                        @endif
                    </div>

                    <div class="mt-4 flex items-center gap-2 border-t border-slate-100 pt-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700" aria-hidden="true">{{ mb_strtoupper(mb_substr($person?->name ?? 'P', 0, 1)) }}</span>
                        <p class="min-w-0 flex-1 truncate text-sm font-medium text-slate-700">{{ $person?->name ?? 'Pengguna' }}</p>
                        <span class="shrink-0 text-xs text-slate-400">{{ $item->created_at->diffForHumans() }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        @if ($offers->hasPages())
        <nav class="mt-6 flex items-center justify-between" aria-label="Navigasi halaman">
            @if ($offers->onFirstPage())
                <span class="rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-300">&laquo; Sebelumnya</span>
            @else
                <a href="{{ $offers->previousPageUrl() }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">&laquo; Sebelumnya</a>
            @endif

            <span class="text-sm text-slate-500">Halaman {{ $offers->currentPage() }} dari {{ $offers->lastPage() }}</span>

            @if ($offers->hasMorePages())
                <a href="{{ $offers->nextPageUrl() }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Berikutnya &raquo;</a>
            @else
                <span class="rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-300">Berikutnya &raquo;</span>
            @endif
        </nav>
        @endif
    @endif
</section>
@endsection