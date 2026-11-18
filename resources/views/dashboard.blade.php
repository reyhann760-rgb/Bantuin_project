{{-- Frontend 2: Dashboard pengguna (customer & penyedia jasa).
     Route (customer.dashboard / provider.dashboard) tetap Route::view, jadi data
     diambil langsung dari relasi model user yang sedang login. Tidak perlu ubah backend. --}}
@extends('layouts.marketplace')

@section('title', 'Dashboard')

@section('content')
@php
    $user = auth()->user();
    $isCustomer = $user->role === 'customer';

    // Daftar milik user (permintaan untuk customer, penawaran untuk provider)
    $listings = $isCustomer ? $user->serviceRequests() : $user->serviceOffers();
    $totalListings = (clone $listings)->count();
    $activeListings = (clone $listings)->where('status', $isCustomer ? 'open' : 'active')->count();
    $recentListings = (clone $listings)->latest()->take(4)->get();

    // Percakapan
    $convQuery = \App\Models\Conversation::where(fn ($q) => $q->where('customer_id', $user->id)->orWhere('provider_id', $user->id));
    $totalChats = (clone $convQuery)->count();
    $ongoingChats = (clone $convQuery)->whereNull('completed_at')->count();
    $recentChats = (clone $convQuery)->with(['customer', 'provider', 'serviceRequest', 'serviceOffer'])->latest('updated_at')->take(4)->get();

    // Notifikasi & ulasan
    $unread = $user->unreadNotifications()->count();
    $reviewCount = $user->reviewsReceived()->count();
    $avgRating = $reviewCount ? round($user->reviewsReceived()->avg('rating'), 1) : null;

    $roleLabel = $isCustomer ? 'Customer' : 'Penyedia Jasa';
    $listingLabel = $isCustomer ? 'Permintaan' : 'Penawaran';
    $createUrl = $isCustomer ? route('service-requests.create') : route('service-offers.create');
    $createLabel = $isCustomer ? '+ Buat Butuh Jasa' : '+ Buat Menyediakan Jasa';
    $mineUrl = $isCustomer ? route('service-requests.mine') : route('service-offers.mine');
    $browseUrl = $isCustomer ? route('sedia.index') : route('butuh.index');
    $browseLabel = $isCustomer ? 'Cari Penyedia Jasa' : 'Cari Permintaan Jasa';
    $showRoute = $isCustomer ? 'service-requests.show' : 'service-offers.show';

    $statusLabel = [
        'open' => ['Terbuka', 'bg-emerald-100 text-emerald-700'],
        'in_progress' => ['Berjalan', 'bg-amber-100 text-amber-700'],
        'completed' => ['Selesai', 'bg-slate-100 text-slate-600'],
        'cancelled' => ['Dibatalkan', 'bg-red-100 text-red-700'],
        'active' => ['Aktif', 'bg-emerald-100 text-emerald-700'],
        'inactive' => ['Nonaktif', 'bg-slate-100 text-slate-600'],
    ];

    $stats = [
        ['label' => $listingLabel . ' aktif', 'value' => $activeListings, 'note' => 'dari ' . $totalListings . ' total', 'url' => $mineUrl],
        ['label' => 'Percakapan berjalan', 'value' => $ongoingChats, 'note' => 'dari ' . $totalChats . ' total', 'url' => route('chat.index')],
        ['label' => 'Notifikasi baru', 'value' => $unread, 'note' => $unread ? 'belum dibaca' : 'semua sudah dibaca', 'url' => route('notifications.index')],
        ['label' => 'Rating ulasan', 'value' => $avgRating ?? '-', 'note' => $reviewCount ? $reviewCount . ' ulasan' : 'belum ada ulasan', 'url' => null],
    ];

    $profileFields = [
        'Nomor telepon' => $user->phone,
        'Kota' => $user->city,
        'Bio singkat' => $user->bio,
    ];
    $missing = collect($profileFields)->filter(fn ($v) => blank($v))->keys();
@endphp

{{-- Header sapaan --}}
<section class="border-b border-slate-200 bg-[#f6f7f2]">
    <div class="mx-auto flex max-w-6xl flex-col gap-5 px-4 py-8 md:flex-row md:items-center md:justify-between">
        <div class="flex items-center gap-4">
            <div class="brand-font flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-emerald-800 text-xl font-bold text-white" aria-hidden="true">
                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-sm text-slate-500">Dashboard {{ $roleLabel }}</p>
                <h1 class="brand-font text-2xl font-extrabold text-slate-900 md:text-3xl">Halo, {{ $user->name }}</h1>
            </div>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row">
            <a href="{{ $browseUrl }}" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">{{ $browseLabel }}</a>
            <a href="{{ $createUrl }}" class="rounded-lg bg-orange-500 px-5 py-2.5 text-center text-sm font-bold text-white hover:bg-orange-600">{{ $createLabel }}</a>
        </div>
    </div>
</section>

<div class="mx-auto max-w-6xl space-y-6 px-4 py-8">

    {{-- Flash message (layout marketplace tidak menampilkannya) --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="status">{{ session('success') }}</div>
    @endif
    @if (session('error') || $errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
            {{ session('error') ?? $errors->first() }}
        </div>
    @endif

    {{-- Ringkasan --}}
    <section aria-label="Ringkasan aktivitas" class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach ($stats as $s)
            @php $tag = $s['url'] ? 'a' : 'div'; @endphp
            <{{ $tag }} @if($s['url']) href="{{ $s['url'] }}" @endif class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm {{ $s['url'] ? 'transition hover:border-emerald-300 hover:shadow-md' : '' }}">
                <p class="text-sm text-slate-500">{{ $s['label'] }}</p>
                <p class="brand-font mt-1 text-3xl font-extrabold text-slate-900">{{ $s['value'] }}</p>
                <p class="mt-1 text-xs text-slate-400">{{ $s['note'] }}</p>
            </{{ $tag }}>
        @endforeach
    </section>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Kolom utama --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Daftar milik user --}}
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h2 class="brand-font text-lg font-bold text-slate-900">{{ $listingLabel }} Saya</h2>
                    <a href="{{ $mineUrl }}" class="text-sm font-semibold text-emerald-700 hover:underline">Lihat semua</a>
                </div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($recentListings as $item)
                        @php [$sLabel, $sClass] = $statusLabel[$item->status] ?? [ucfirst($item->status), 'bg-slate-100 text-slate-600']; @endphp
                        <li>
                            <a href="{{ route($showRoute, $item) }}" class="flex items-center justify-between gap-3 px-5 py-3.5 hover:bg-slate-50">
                                <div class="min-w-0">
                                    <p class="truncate font-medium text-slate-900">{{ $item->title }}</p>
                                    <p class="text-xs text-slate-400">Dibuat {{ $item->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="shrink-0 rounded-full px-3 py-1 text-xs font-medium {{ $sClass }}">{{ $sLabel }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="px-5 py-10 text-center">
                            <p class="font-medium text-slate-700">Belum ada {{ strtolower($listingLabel) }}.</p>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $isCustomer ? 'Ceritakan jasa yang kamu butuhkan agar penyedia jasa bisa menghubungimu.' : 'Tawarkan keahlianmu agar customer bisa menemukanmu.' }}
                            </p>
                            <a href="{{ $createUrl }}" class="mt-4 inline-block rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-800">{{ $createLabel }}</a>
                        </li>
                    @endforelse
                </ul>
            </section>

            {{-- Percakapan terbaru --}}
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h2 class="brand-font text-lg font-bold text-slate-900">Percakapan Terbaru</h2>
                    <a href="{{ route('chat.index') }}" class="text-sm font-semibold text-emerald-700 hover:underline">Buka chat</a>
                </div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($recentChats as $conv)
                        @php
                            $other = $conv->getOtherParticipant($user);
                            $topic = $conv->serviceRequest?->title ?? $conv->serviceOffer?->title ?? 'Percakapan umum';
                        @endphp
                        <li>
                            <a href="{{ route('chat.show', $conv) }}" class="flex items-center gap-3 px-5 py-3.5 hover:bg-slate-50">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700" aria-hidden="true">
                                    {{ mb_strtoupper(mb_substr($other?->name ?? 'P', 0, 1)) }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-medium text-slate-900">{{ $other?->name ?? 'Pengguna' }}</p>
                                    <p class="truncate text-sm text-slate-500">{{ $topic }}</p>
                                </div>
                                <div class="shrink-0 text-right">
                                    @if ($conv->isCompleted())
                                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">Selesai</span>
                                    @endif
                                    <p class="mt-1 text-xs text-slate-400">{{ $conv->updated_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="px-5 py-10 text-center">
                            <p class="font-medium text-slate-700">Belum ada percakapan.</p>
                            <p class="mt-1 text-sm text-slate-500">Mulai chat dari halaman {{ $isCustomer ? 'Menyediakan Jasa' : 'Butuh Jasa' }}.</p>
                        </li>
                    @endforelse
                </ul>
            </section>
        </div>

        {{-- Kolom samping --}}
        <aside class="space-y-6">

            {{-- Info akun --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="brand-font text-lg font-bold text-slate-900">Informasi Akun</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div>
                        <dt class="text-slate-400">Email</dt>
                        <dd class="break-all font-medium text-slate-800">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">Nomor telepon</dt>
                        <dd class="font-medium text-slate-800">{{ $user->phone ?: 'Belum diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">Kota</dt>
                        <dd class="font-medium text-slate-800">{{ $user->city ?: 'Belum diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">Peran</dt>
                        <dd><span class="rounded-full bg-emerald-100 px-3 py-0.5 text-xs font-semibold text-emerald-700">{{ $roleLabel }}</span></dd>
                    </div>
                </dl>

                @if ($missing->isNotEmpty())
                    <p class="mt-4 rounded-lg bg-amber-50 p-3 text-xs text-amber-800">
                        Lengkapi profilmu: {{ $missing->join(', ', ' dan ') }} belum diisi.
                    </p>
                @endif

                {{-- Route profil dikerjakan di halaman Profil (Frontend 2 bagian berikutnya). --}}
                @if (\Illuminate\Support\Facades\Route::has('profile.edit'))
                    <a href="{{ route('profile.edit') }}" class="mt-4 block rounded-lg border border-slate-300 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Edit Profil</a>
                @endif
            </section>

            {{-- Pintasan --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="brand-font text-lg font-bold text-slate-900">Pintasan</h2>
                <ul class="mt-3 space-y-1 text-sm font-medium">
                    <li><a href="{{ $createUrl }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-slate-50">{{ $createLabel }}</a></li>
                    <li><a href="{{ $mineUrl }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-slate-50">{{ $listingLabel }} Saya</a></li>
                    <li><a href="{{ route('chat.index') }}" class="block rounded-lg px-3 py-2 text-slate-700 hover:bg-slate-50">Chat</a></li>
                    <li>
                        <a href="{{ route('notifications.index') }}" class="flex items-center justify-between rounded-lg px-3 py-2 text-slate-700 hover:bg-slate-50">
                            Notifikasi
                            @if ($unread)
                                <span class="rounded-full bg-orange-500 px-2 py-0.5 text-xs font-bold text-white">{{ $unread }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
                <form action="{{ route('logout') }}" method="POST" class="mt-3 border-t border-slate-100 pt-3">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50">Keluar</button>
                </form>
            </section>
        </aside>
    </div>
</div>
@endsection