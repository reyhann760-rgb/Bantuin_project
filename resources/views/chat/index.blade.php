{{-- Frontend 2: Halaman Pesan Masuk (Inbox Chat).
     Mengikuti standar desain layouts/marketplace, palet warna Bantuin, dan responsif. --}}
@extends('layouts.marketplace')

@section('title', 'Pesan Masuk (Chat)')

@section('content')
@php
    $user = Auth::user();
@endphp

{{-- Header Inbox --}}
<section class="border-b border-slate-200 bg-[#f6f7f2]">
    <div class="mx-auto flex max-w-5xl flex-col gap-4 px-4 py-8 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-800 hover:text-emerald-900 hover:underline">
                &larr; Kembali ke Dashboard
            </a>
            <h1 class="brand-font mt-2 text-2xl font-extrabold text-slate-900 md:text-3xl">Pesan Masuk</h1>
            <p class="mt-1 text-sm text-slate-500">Daftar percakapan aktif dengan pelanggan atau penyedia jasa Anda.</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="rounded-full bg-emerald-100 px-3.5 py-1 text-xs font-semibold text-emerald-800">
                {{ $conversations->total() }} Percakapan
            </span>
        </div>
    </div>
</section>

<div class="mx-auto max-w-5xl px-4 py-8">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm" role="status">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- List Percakapan --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @forelse($conversations as $conversation)
            @php
                $otherUser = $conversation->getOtherParticipant($user);
                $lastMessage = $conversation->messages->first();
                $listing = $conversation->serviceRequest ?? $conversation->serviceOffer;
                $listingTitle = $listing?->title ?? 'Percakapan Langsung';
                $isButuh = (bool) $conversation->serviceRequest;
                $listingType = $isButuh ? 'Butuh Jasa' : ($conversation->serviceOffer ? 'Menyediakan Jasa' : 'Umum');
                $isCompleted = $conversation->isCompleted();
            @endphp
            <a href="{{ route('chat.show', $conversation) }}"
               class="group block border-b border-slate-100 p-4.5 transition hover:bg-slate-50/80 md:p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex min-w-0 items-start gap-3.5">
                        {{-- Avatar Lawan Bicara --}}
                        <div class="relative flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-emerald-800 font-bold text-white shadow-inner">
                            @if ($otherUser?->avatar)
                                <img src="{{ asset('storage/' . $otherUser->avatar) }}" alt="{{ $otherUser->name }}" class="h-full w-full object-cover">
                            @else
                                <span class="text-base uppercase">{{ mb_substr($otherUser?->name ?? 'P', 0, 1) }}</span>
                            @endif
                        </div>

                        {{-- Info & Cuplikan Pesan --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-bold text-slate-900 group-hover:text-emerald-800">
                                    {{ $otherUser?->name ?? 'Pengguna' }}
                                </span>
                                <span class="rounded-md px-2 py-0.5 text-[11px] font-semibold {{ $isButuh ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700' }}">
                                    {{ $listingType }}
                                </span>
                                @if($isCompleted)
                                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600">
                                        &check; Selesai
                                    </span>
                                @endif
                            </div>

                            <p class="mt-0.5 truncate text-xs font-semibold text-slate-500">
                                {{ $listingTitle }}
                            </p>

                            <p class="mt-1 truncate text-sm text-slate-600">
                                @if($lastMessage)
                                    <span class="{{ $lastMessage->sender_id === $user->id ? 'text-slate-400' : 'font-medium text-slate-700' }}">
                                        {{ $lastMessage->sender_id === $user->id ? 'Anda: ' : '' }}{{ $lastMessage->body }}
                                    </span>
                                @else
                                    <span class="italic text-slate-400">Belum ada pesan. Klik untuk mulai mengobrol.</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Waktu Pesan Terakhir --}}
                    <div class="shrink-0 text-right">
                        <span class="text-xs text-slate-400">
                            {{ $lastMessage?->created_at?->diffForHumans() ?? $conversation->updated_at->diffForHumans() }}
                        </span>
                        <div class="mt-2 text-right">
                            <span class="inline-flex items-center text-xs font-semibold text-emerald-700 opacity-0 transition group-hover:opacity-100">
                                Buka &rarr;
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="p-12 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <h3 class="brand-font mt-3 text-lg font-bold text-slate-800">Belum Ada Percakapan</h3>
                <p class="mt-1 text-sm text-slate-500">Anda belum memiliki riwayat obrolan. Hubungi pelanggan atau penyedia jasa dari daftar layanan.</p>
                <div class="mt-5 flex justify-center gap-3">
                    <a href="{{ route('sedia.index') }}" class="rounded-lg bg-emerald-800 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-900 transition">
                        Cari Penyedia Jasa
                    </a>
                    <a href="{{ route('butuh.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                        Cari Kebutuhan Jasa
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($conversations->hasPages())
        <div class="mt-6">
            {{ $conversations->links() }}
        </div>
    @endif
</div>
@endsection
