{{-- Frontend 2: Halaman Profil Publik Pengguna (Penawaran Aktif, Portofolio/Kebutuhan, & Ulasan).
     Mengikuti tema layouts/marketplace, konsisten dengan kartu jasa dan palet Bantuin. --}}
@extends('layouts.marketplace')

@section('title', 'Profil ' . ($user->name ?? 'Pengguna'))

@section('content')
@php
    $targetUser = $user ?? auth()->user();
    $isOwner = auth()->check() && auth()->id() === $targetUser->id;
    $isProvider = $targetUser->role === 'provider';
    $isCustomer = $targetUser->role === 'customer';

    // Penawaran aktif (untuk provider)
    $offers = $offers ?? ($isProvider ? $targetUser->serviceOffers()->where('status', 'active')->with('category')->latest()->get() : collect());

    // Kebutuhan terbuka (untuk customer jika dibuka)
    $requests = $requests ?? ($isCustomer ? $targetUser->serviceRequests()->where('status', 'open')->with('category')->latest()->get() : collect());

    // Ulasan diterima
    $reviews = $reviews ?? $targetUser->reviewsReceived()->with('reviewer')->latest()->get();
    $reviewCount = $reviews->count();
    $avgRating = $reviewCount ? round($reviews->avg('rating'), 1) : null;

    $roleLabel = match ($targetUser->role) {
        'provider' => 'Penyedia Jasa',
        'customer' => 'Pelanggan',
        'admin' => 'Administrator',
        default => ucfirst($targetUser->role ?? 'Pengguna')
    };

    $editProfileUrl = \Illuminate\Support\Facades\Route::has('profile.edit') 
        ? route('profile.edit') 
        : url('/profil');
@endphp

{{-- Header Profil / Banner --}}
<section class="border-b border-slate-200 bg-[#f6f7f2]">
    <div class="mx-auto max-w-5xl px-4 py-8 md:py-12">
        <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                {{-- Foto Profil / Avatar --}}
                <div class="relative flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-emerald-800 text-3xl font-extrabold text-white shadow-md">
                    @if ($targetUser->avatar)
                        <img src="{{ asset('storage/' . $targetUser->avatar) }}" alt="{{ $targetUser->name }}" class="h-full w-full object-cover">
                    @else
                        <span>{{ mb_strtoupper(mb_substr($targetUser->name ?? 'U', 0, 1)) }}</span>
                    @endif
                </div>

                {{-- Detail Utama --}}
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="brand-font text-2xl font-extrabold text-slate-900 md:text-3xl">{{ $targetUser->name }}</h1>
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-0.5 text-xs font-semibold text-emerald-800">
                            {{ $roleLabel }}
                        </span>
                    </div>

                    <div class="mt-2 flex flex-wrap items-center gap-y-1 gap-x-4 text-sm text-slate-500">
                        @if ($targetUser->city)
                            <span class="inline-flex items-center gap-1">
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $targetUser->city }}
                            </span>
                        @endif

                        <span class="inline-flex items-center gap-1">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Bergabung {{ $targetUser->created_at ? $targetUser->created_at->format('M Y') : '-' }}
                        </span>

                        @if ($isProvider && $avgRating)
                            <span class="inline-flex items-center gap-1 font-medium text-amber-600">
                                <svg class="h-4 w-4 fill-amber-400 text-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                {{ $avgRating }} ({{ $reviewCount }} ulasan)
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi Pemilik --}}
            @if ($isOwner)
                <div>
                    <a href="{{ $editProfileUrl }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                        <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Edit Profil
                    </a>
                </div>
            @endif
        </div>

        {{-- Bio Profil --}}
        @if ($targetUser->bio)
            <div class="mt-6 rounded-xl border border-slate-200/80 bg-white/70 p-4 backdrop-blur-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tentang</p>
                <p class="mt-1 whitespace-pre-line text-sm text-slate-700 leading-relaxed">{{ $targetUser->bio }}</p>
            </div>
        @endif
    </div>
</section>

{{-- Konten Utama --}}
<div class="mx-auto max-w-5xl space-y-10 px-4 py-8">

    {{-- Bagian 1: Layanan Aktif (Jika Penyedia Jasa) --}}
    @if ($isProvider)
        <section>
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h2 class="brand-font text-xl font-bold text-slate-900">Layanan yang Ditawarkan</h2>
                <span class="text-xs font-medium text-slate-500">{{ $offers->count() }} layanan aktif</span>
            </div>

            @if ($offers->isNotEmpty())
                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($offers as $offer)
                        <div class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md">
                            <div>
                                @if ($offer->category)
                                    <span class="inline-block rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                        {{ $offer->category->name }}
                                    </span>
                                @endif
                                <h3 class="mt-2.5 font-bold text-slate-900 line-clamp-2">
                                    <a href="{{ route('service-offers.show', $offer) }}" class="hover:text-emerald-700">
                                        {{ $offer->title }}
                                    </a>
                                </h3>
                                <p class="mt-2 text-xs text-slate-500 line-clamp-3 leading-relaxed">
                                    {{ $offer->description }}
                                </p>
                            </div>

                            <div class="mt-5 border-t border-slate-100 pt-3 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] text-slate-400 block">Harga</span>
                                    <span class="font-bold text-sm text-slate-900">
                                        @if ($offer->price)
                                            Rp {{ number_format($offer->price, 0, ',', '.') }}
                                        @else
                                            Nego / Disesuaikan
                                        @endif
                                    </span>
                                </div>
                                <a href="{{ route('service-offers.show', $offer) }}" class="rounded-lg bg-emerald-800 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-emerald-900 transition">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-8 text-center">
                    <p class="font-medium text-slate-600">Penyedia ini belum mempublikasikan layanan aktif.</p>
                </div>
            @endif
        </section>
    @endif

    {{-- Bagian 2: Kebutuhan Terbuka (Jika Pelanggan) --}}
    @if ($isCustomer && $requests->isNotEmpty())
        <section>
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h2 class="brand-font text-xl font-bold text-slate-900">Kebutuhan Jasa Terbuka</h2>
                <span class="text-xs font-medium text-slate-500">{{ $requests->count() }} permintaan</span>
            </div>

            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($requests as $req)
                    <div class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md">
                        <div>
                            @if ($req->category)
                                <span class="inline-block rounded-md bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                    {{ $req->category->name }}
                                </span>
                            @endif
                            <h3 class="mt-2.5 font-bold text-slate-900 line-clamp-2">
                                <a href="{{ route('service-requests.show', $req) }}" class="hover:text-indigo-700">
                                    {{ $req->title }}
                                </a>
                            </h3>
                            <p class="mt-2 text-xs text-slate-500 line-clamp-3 leading-relaxed">
                                {{ $req->description }}
                            </p>
                        </div>

                        <div class="mt-5 border-t border-slate-100 pt-3 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-400 block">Anggaran</span>
                                <span class="font-bold text-sm text-slate-900">
                                    @if ($req->budget)
                                        Rp {{ number_format($req->budget, 0, ',', '.') }}
                                    @else
                                        Terbuka
                                    @endif
                                </span>
                            </div>
                            <a href="{{ route('service-requests.show', $req) }}" class="rounded-lg bg-indigo-600 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700 transition">
                                Detail
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Bagian 3: Ulasan Pelanggan --}}
    <section>
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <div>
                <h2 class="brand-font text-xl font-bold text-slate-900">Ulasan & Reputasi</h2>
                <p class="text-xs text-slate-500">Ulasan yang diberikan oleh pengguna setelah pekerjaan selesai.</p>
            </div>
            @if ($avgRating)
                <div class="flex items-center gap-1.5 rounded-lg bg-amber-50 px-3 py-1.5 text-amber-700">
                    <svg class="h-4 w-4 fill-amber-400 text-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <span class="text-sm font-bold">{{ $avgRating }}</span>
                    <span class="text-xs text-amber-600">/ 5.0</span>
                </div>
            @endif
        </div>

        @if ($reviews->isNotEmpty())
            <div class="mt-6 space-y-4">
                @foreach ($reviews as $rev)
                    @php $reviewer = $rev->reviewer; @endphp
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-700">
                                    {{ mb_strtoupper(mb_substr($reviewer?->name ?? 'P', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $reviewer?->name ?? 'Pelanggan' }}</p>
                                    <div class="flex items-center gap-1 text-xs text-amber-500">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $rev->rating)
                                                <span>&#9733;</span>
                                            @else
                                                <span class="text-slate-200">&#9733;</span>
                                            @endif
                                        @endfor
                                        <span class="ml-1 text-slate-400">({{ $rev->rating }}/5)</span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-xs text-slate-400">
                                {{ $rev->created_at ? $rev->created_at->diffForHumans() : '-' }}
                            </span>
                        </div>

                        @if ($rev->comment)
                            <p class="mt-3 text-sm text-slate-600 leading-relaxed italic bg-slate-50 p-3 rounded-lg border border-slate-100">
                                "{{ $rev->comment }}"
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-8 text-center">
                <p class="font-medium text-slate-600">Belum ada ulasan yang diterima.</p>
                <p class="mt-1 text-xs text-slate-400">Ulasan akan muncul di sini setelah pekerjaan diselesaikan oleh pelanggan.</p>
            </div>
        @endif
    </section>

</div>
@endsection

