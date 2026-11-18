{{-- Frontend 2: halaman Registrasi. Route & nama field tidak diubah. --}}
@extends('layouts.marketplace')

@section('title', 'Daftar')

@section('content')
@php
    $field = fn ($name) => 'w-full rounded-lg border bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 outline-none focus:ring-2 '
        . ($errors->has($name) ? 'border-red-400 focus:ring-red-200' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-200');
    $roles = [
        'customer' => ['Customer', 'Saya butuh jasa'],
        'provider' => ['Penyedia Jasa', 'Saya menawarkan jasa'],
    ];
@endphp

<section class="bg-[#f6f7f2]">
    <div class="mx-auto flex max-w-lg items-center px-4 py-12 md:py-16">
        <div class="w-full">

            <div class="mb-6 text-center">
                <h1 class="brand-font text-3xl font-extrabold text-slate-900">Daftar di <span class="text-emerald-700">Bantuin</span></h1>
                <p class="mt-2 text-sm text-slate-500">Buat akun untuk mulai mencari atau menawarkan jasa.</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">

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

                <form action="{{ route('register.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama lengkap <span class="text-red-500">*</span></label>
                        <input id="name" type="text" name="name" required maxlength="255"
                               value="{{ old('name') }}" placeholder="Masukkan nama lengkap"
                               class="{{ $field('name') }}">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email <span class="text-red-500">*</span></label>
                            <input id="email" type="email" name="email" required
                                   value="{{ old('email') }}" placeholder="nama@email.com"
                                   class="{{ $field('email') }}">
                            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="phone" class="mb-1.5 block text-sm font-semibold text-slate-700">Nomor telepon</label>
                            <input id="phone" type="text" name="phone" maxlength="20" inputmode="tel"
                                   value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                                   class="{{ $field('phone') }}">
                            @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Pilihan role --}}
                    <div>
                        <span class="mb-1.5 block text-sm font-semibold text-slate-700">Daftar sebagai <span class="text-red-500">*</span></span>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach ($roles as $value => [$label, $desc])
                                <label class="cursor-pointer">
                                    <input type="radio" name="role" value="{{ $value }}" required class="peer sr-only"
                                           @checked(old('role') === $value)>
                                    <div class="rounded-lg border border-slate-300 bg-white p-3 transition
                                                peer-checked:border-emerald-600 peer-checked:bg-emerald-50
                                                peer-focus-visible:ring-2 peer-focus-visible:ring-emerald-200 hover:bg-slate-50">
                                        <p class="text-sm font-semibold text-slate-800">{{ $label }}</p>
                                        <p class="text-xs text-slate-500">{{ $desc }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('role') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Password <span class="text-red-500">*</span></label>
                            <input id="password" type="password" name="password" required minlength="8"
                                   placeholder="Minimal 8 karakter" class="{{ $field('password') }}">
                            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-slate-700">Konfirmasi password <span class="text-red-500">*</span></label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                   placeholder="Ulangi password" class="{{ $field('password_confirmation') }}">
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                        Daftar Sekarang
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-sm text-slate-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</section>
@endsection