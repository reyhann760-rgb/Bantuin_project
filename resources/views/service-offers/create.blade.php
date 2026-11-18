{{-- Frontend 2: halaman Buat Menyediakan Jasa. Nama route & field tidak diubah. --}}
@extends('layouts.marketplace')

@section('title', 'Buat Menyediakan Jasa')

@section('content')
<section class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-3xl px-4 py-8">
        <a href="{{ route('service-offers.mine') }}" class="text-sm font-medium text-indigo-600 hover:underline">&larr; Kembali</a>
        <h1 class="mt-3 text-2xl font-bold md:text-3xl">Buat Menyediakan Jasa</h1>
        <p class="mt-1 text-slate-500">Tawarkan jasa yang bisa kamu berikan agar mudah ditemukan pelanggan.</p>
    </div>
</section>

<section class="mx-auto max-w-3xl px-4 py-8">

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Periksa kembali isian kamu:</p>
            <ul class="mt-1 list-disc space-y-0.5 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('service-offers.store') }}" method="POST"
          class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
        @csrf

        @include('service-offers._form')

        <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('service-offers.mine') }}"
               class="rounded-lg border border-slate-300 px-5 py-2.5 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
            <button type="submit"
                    class="rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Simpan Penawaran</button>
        </div>
    </form>
</section>
@endsection
