{{-- Frontend 2: halaman Edit Menyediakan Jasa. Nama route & field tidak diubah. --}}
@extends('layouts.marketplace')

@section('title', 'Edit Menyediakan Jasa')

@section('content')
<section class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-3xl px-4 py-8">
        <a href="{{ route('service-offers.show', $serviceOffer) }}" class="text-sm font-medium text-indigo-600 hover:underline">&larr; Kembali</a>
        <h1 class="mt-3 text-2xl font-bold md:text-3xl">Edit Menyediakan Jasa</h1>
        <p class="mt-1 text-slate-500">Perbarui detail penawaran atau nonaktifkan sementara.</p>
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

    <form action="{{ route('service-offers.update', $serviceOffer) }}" method="POST"
          class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
        @csrf
        @method('PUT')

        @include('service-offers._form')

        <div class="mt-5 sm:max-w-xs">
            <label for="status" class="mb-1.5 block text-sm font-semibold text-slate-700">Status</label>
            <select id="status" name="status"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                @foreach (['active' => 'Aktif', 'inactive' => 'Nonaktif'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $serviceOffer->status) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('service-offers.show', $serviceOffer) }}"
               class="rounded-lg border border-slate-300 px-5 py-2.5 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
            <button type="submit"
                    class="rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Simpan Perubahan</button>
        </div>
    </form>

    <form action="{{ route('service-offers.destroy', $serviceOffer) }}" method="POST"
          onsubmit="return confirm('Yakin ingin menghapus penawaran ini?')"
          class="mt-6 flex items-center justify-between rounded-2xl border border-red-200 bg-red-50 p-5">
        @csrf
        @method('DELETE')
        <div>
            <p class="text-sm font-semibold text-red-700">Hapus penawaran</p>
            <p class="text-xs text-red-600">Tindakan ini tidak bisa dibatalkan.</p>
        </div>
        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Hapus</button>
    </form>
</section>
@endsection
