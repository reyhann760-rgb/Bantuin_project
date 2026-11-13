@extends('layouts.marketplace')
@section('title', $judulHalaman)

@section('content')
@php $routeName = $type === 'butuh' ? 'butuh.index' : 'sedia.index'; @endphp
<section class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-6xl px-4 py-8">
        <h1 class="text-2xl font-bold md:text-3xl">{{ $judulHalaman }}</h1>
        <p class="mt-1 text-slate-500">{{ $subjudul }}</p>

        <form action="{{ route($routeName) }}" method="GET" class="mt-5 grid gap-2 sm:grid-cols-[1fr_200px_auto]">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul atau deskripsi..."
                   class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm placeholder-slate-400 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
            <select name="kategori"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                <option value="">Semua kategori</option>
                @foreach ($kategori as $k)
                    <option value="{{ $k }}" @selected(request('kategori') === $k)>{{ $k }}</option>
                @endforeach
            </select>
            <button class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Filter</button>
        </form>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-8">
    @if ($items->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-16 text-center">
            <p class="font-semibold">Belum ada hasil</p>
            <p class="mt-1 text-sm text-slate-500">Coba ubah kata kunci atau kategori.</p>
            <a href="{{ route($routeName) }}" class="mt-4 inline-block text-sm font-medium text-indigo-600 hover:underline">Reset filter</a>
        </div>
    @else
        <p class="mb-4 text-sm text-slate-500">{{ $items->count() }} hasil ditemukan</p>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($items as $item)
                <x-service-card :item="$item" :type="$type" />
            @endforeach
        </div>
    @endif
</section>
@endsection