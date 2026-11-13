@extends('layouts.app')
@section('title', 'Edit profil')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-10">
    <h1 class="text-2xl font-bold text-gray-900">Edit profil</h1>

    <form method="POST" action="{{ route('profile.update') }}" class="mt-8 space-y-5 rounded-xl border border-gray-200 bg-white p-6">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama lengkap</label>
            <input id="name" name="name" value="{{ old('name', $user->name) }}" required
                   class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                   class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Nomor telepon</label>
            <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                   class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="bio" class="block text-sm font-medium text-gray-700">Tentang saya</label>
            <textarea id="bio" name="bio" rows="4"
                      class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('bio', $user->bio) }}</textarea>
            @error('bio') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('profile.show') }}" class="rounded-lg px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Simpan perubahan</button>
        </div>
    </form>
</div>
@endsection
