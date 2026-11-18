{{-- Frontend 2: halaman Login. Route & nama field tidak diubah. --}}
@extends('layouts.marketplace')

@section('title', 'Masuk')

@section('content')
@php
    $field = fn ($name) => 'w-full rounded-lg border bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 outline-none focus:ring-2 '
        . ($errors->has($name) ? 'border-red-400 focus:ring-red-200' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-200');
@endphp

<section class="bg-[#f6f7f2]">
    <div class="mx-auto flex max-w-md items-center px-4 py-12 md:py-16">
        <div class="w-full">

            <div class="mb-6 text-center">
                <h1 class="brand-font text-3xl font-extrabold text-slate-900">Masuk ke <span class="text-emerald-700">Bantuin</span></h1>
                <p class="mt-2 text-sm text-slate-500">Lanjutkan mencari atau menawarkan jasa di sekitarmu.</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">

                {{-- Pesan sukses (mis. setelah logout) --}}
                @if (session('success'))
                    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('login.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email</label>
                        <input id="email" type="email" name="email" required autofocus
                               value="{{ old('email') }}"
                               placeholder="nama@email.com"
                               class="{{ $field('email') }}">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Password</label>
                        <input id="password" type="password" name="password" required
                               placeholder="Masukkan password"
                               class="{{ $field('password') }}">
                        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember" value="1"
                               class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-200">
                        Ingat saya
                    </label>

                    <button type="submit"
                            class="w-full rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                        Masuk
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-sm text-slate-600">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:underline">Daftar sekarang</a>
            </p>
        </div>
    </div>
</section>
@endsection