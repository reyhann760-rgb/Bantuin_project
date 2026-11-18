{{-- Frontend 2: Halaman Edit Profil Pengguna (Ubah Data Diri, Avatar, & Password).
     Mengikuti tema layouts/marketplace dan spesifikasi PRD/TEAM_TASK_ASSIGNMENT. --}}
@extends('layouts.marketplace')

@section('title', 'Edit Profil')

@section('content')
@php
    $field = fn ($name, $pad = 'px-4') => 'w-full rounded-lg border bg-white ' . $pad . ' py-2.5 text-sm text-slate-800 placeholder-slate-400 outline-none focus:ring-2 '
        . ($errors->has($name) ? 'border-red-400 focus:ring-red-200' : 'border-slate-300 focus:border-emerald-600 focus:ring-emerald-100');

    // Data user login (atau data yang diteruskan controller)
    $currentUser = $user ?? auth()->user();
    $roleLabel = match ($currentUser?->role) {
        'provider' => 'Penyedia Jasa',
        'customer' => 'Pelanggan',
        'admin' => 'Administrator',
        default => ucfirst($currentUser?->role ?? 'Pengguna')
    };

    // Penentuan route aksi form profil dan ganti password
    $profileAction = \Illuminate\Support\Facades\Route::has('profile.update') 
        ? route('profile.update') 
        : url('/profil');

    $passwordAction = \Illuminate\Support\Facades\Route::has('profile.password') 
        ? route('profile.password') 
        : url('/profil/password');

    $publicProfileUrl = \Illuminate\Support\Facades\Route::has('profile.show') 
        ? route('profile.show', $currentUser) 
        : url('/pengguna/' . $currentUser->id);
@endphp

{{-- Header Halaman --}}
<section class="border-b border-slate-200 bg-[#f6f7f2]">
    <div class="mx-auto flex max-w-4xl flex-col gap-4 px-4 py-8 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-800 hover:text-emerald-900 hover:underline">
                &larr; Kembali ke Dashboard
            </a>
            <h1 class="brand-font mt-2 text-2xl font-extrabold text-slate-900 md:text-3xl">Pengaturan Profil</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola informasi publik, data kontak, dan keamanan akun Anda.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ $publicProfileUrl }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-center text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                Lihat Profil Publik
            </a>
        </div>
    </div>
</section>

<div class="mx-auto max-w-4xl space-y-6 px-4 py-8">

    {{-- Flash Notifications --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm" role="status">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm">
            <p class="font-semibold">Mohon periksa kembali form berikut:</p>
            <ul class="mt-1 list-disc space-y-0.5 pl-5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form 1: Ubah Biodata & Avatar --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
        <div class="border-b border-slate-100 pb-5">
            <h2 class="brand-font text-lg font-bold text-slate-900">Biodata Diri</h2>
            <p class="mt-0.5 text-sm text-slate-500">Perbarui identitas dan informasi kontak yang tampil pada platform.</p>
        </div>

        <form action="{{ $profileAction }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Avatar & Preview --}}
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Foto Profil (Avatar)</label>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                    <div class="relative flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-full border-2 border-slate-200 bg-emerald-800 text-2xl font-bold text-white shadow-inner">
                        @if ($currentUser->avatar)
                            <img id="avatar-preview-img" src="{{ asset('storage/' . $currentUser->avatar) }}" alt="{{ $currentUser->name }}" class="h-full w-full object-cover">
                        @else
                            <span id="avatar-initial">{{ mb_strtoupper(mb_substr($currentUser->name ?? 'U', 0, 1)) }}</span>
                            <img id="avatar-preview-img" src="" alt="Preview" class="hidden h-full w-full object-cover">
                        @endif
                    </div>
                    <div class="flex-1 space-y-1">
                        <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100 file:cursor-pointer"
                               onchange="previewAvatar(event)">
                        <p class="text-xs text-slate-400">Format yang didukung: JPG, PNG, atau WEBP. Maksimal 2MB.</p>
                        @error('avatar') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Nama & Role --}}
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input id="name" type="text" name="name" required maxlength="255"
                           value="{{ old('name', $currentUser->name) }}"
                           placeholder="Nama lengkap Anda"
                           class="{{ $field('name') }}">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Peran Akun</label>
                    <div class="flex h-[42px] items-center">
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">
                            {{ $roleLabel }}
                        </span>
                        <span class="ml-2 text-xs text-slate-400">(Tipe akun tidak dapat diubah)</span>
                    </div>
                </div>
            </div>

            {{-- Email (Read-Only) & Nomor HP --}}
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Alamat Email</label>
                    <input id="email" type="email" value="{{ $currentUser->email }}" disabled
                           class="w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-500">
                    <p class="mt-1 text-xs text-slate-400">Alamat email terdaftar dan terikat akun.</p>
                </div>

                <div>
                    <label for="phone" class="mb-1.5 block text-sm font-semibold text-slate-700">Nomor Telepon / WhatsApp</label>
                    <input id="phone" type="tel" name="phone" maxlength="20"
                           value="{{ old('phone', $currentUser->phone) }}"
                           placeholder="Contoh: 081234567890"
                           class="{{ $field('phone') }}">
                    @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Kota --}}
            <div>
                <label for="city" class="mb-1.5 block text-sm font-semibold text-slate-700">Kota Domisili</label>
                <input id="city" type="text" name="city" maxlength="100"
                       value="{{ old('city', $currentUser->city) }}"
                       placeholder="Contoh: Surabaya, Jakarta Selatan, Bandung"
                       class="{{ $field('city') }}">
                @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Bio Singkat --}}
            <div>
                <label for="bio" class="mb-1.5 block text-sm font-semibold text-slate-700">Bio / Deskripsi Diri</label>
                <textarea id="bio" name="bio" rows="4" maxlength="500"
                          placeholder="Jelaskan keahlian atau ringkasan diri Anda secara singkat..."
                          class="{{ $field('bio') }}">{{ old('bio', $currentUser->bio) }}</textarea>
                <p class="mt-1 text-xs text-slate-400">Maksimal 500 karakter. Ditampilkan pada profil publik Anda.</p>
                @error('bio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="rounded-lg bg-emerald-800 px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-emerald-900 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </section>

    {{-- Form 2: Ganti Password --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
        <div class="border-b border-slate-100 pb-5">
            <h2 class="brand-font text-lg font-bold text-slate-900">Keamanan Akun</h2>
            <p class="mt-0.5 text-sm text-slate-500">Perbarui kata sandi akun untuk menjaga keamanan akun Anda.</p>
        </div>

        <form action="{{ $passwordAction }}" method="POST" class="mt-6 space-y-5">
            @csrf
            @method('PUT')

            {{-- Password Saat Ini --}}
            <div>
                <label for="current_password" class="mb-1.5 block text-sm font-semibold text-slate-700">Kata Sandi Saat Ini <span class="text-red-500">*</span></label>
                <input id="current_password" type="password" name="current_password" required
                       placeholder="••••••••"
                       class="{{ $field('current_password') }}">
                @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Password Baru & Konfirmasi --}}
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Kata Sandi Baru <span class="text-red-500">*</span></label>
                    <input id="password" type="password" name="password" required minlength="8"
                           placeholder="Minimal 8 karakter"
                           class="{{ $field('password') }}">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-slate-700">Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span></label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8"
                           placeholder="Ulangi kata sandi baru"
                           class="{{ $field('password_confirmation') }}">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="rounded-lg bg-slate-800 px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-slate-900 transition">
                    Perbarui Kata Sandi
                </button>
            </div>
        </form>
    </section>

</div>

{{-- Skrip Preview Avatar --}}
<script>
function previewAvatar(event) {
    const input = event.target;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewImg = document.getElementById('avatar-preview-img');
            const initialSpan = document.getElementById('avatar-initial');
            if (previewImg) {
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
            }
            if (initialSpan) {
                initialSpan.classList.add('hidden');
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection

