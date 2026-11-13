@extends('layouts.app')
@section('title', 'Profil')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-10">
    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-2xl font-bold text-indigo-700">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $user->name }}</h1>
                <p class="text-sm text-gray-600">{{ $user->email }}</p>
            </div>
        </div>
        <dl class="mt-6 space-y-3 text-sm">
            <div><dt class="text-gray-500">Nomor telepon</dt><dd class="text-gray-900">{{ $user->phone ?? 'Belum diisi' }}</dd></div>
            <div><dt class="text-gray-500">Tentang saya</dt><dd class="text-gray-900">{{ $user->bio ?? 'Belum diisi' }}</dd></div>
        </dl>
        <a href="{{ route('profile.edit') }}" class="mt-6 inline-block rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Edit profil</a>
    </div>
</div>
@endsection
