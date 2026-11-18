{{-- Halaman Notifikasi.
     Mengikuti standar desain layouts/marketplace (sama seperti chat/index): header hijau-krem,
     kartu rounded-2xl, palet emerald/indigo Bantuin, dan responsif. --}}
@extends('layouts.marketplace')

@section('title', 'Notifikasi')

@section('content')

{{-- Header Notifikasi --}}
<section class="border-b border-slate-200 bg-[#f6f7f2]">
    <div class="mx-auto flex max-w-5xl flex-col gap-4 px-4 py-8 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-800 hover:text-emerald-900 hover:underline">
                &larr; Kembali ke Dashboard
            </a>
            <h1 class="brand-font mt-2 text-2xl font-extrabold text-slate-900 md:text-3xl">Notifikasi</h1>
            <p class="mt-1 text-sm text-slate-500">Pemberitahuan pesan dan aktivitas terbaru Anda.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($unreadCount > 0)
                <span class="rounded-full bg-indigo-100 px-3.5 py-1 text-xs font-semibold text-indigo-700">
                    {{ $unreadCount }} Belum Dibaca
                </span>
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-600 bg-white px-4 py-2 text-xs font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Tandai Semua Dibaca
                    </button>
                </form>
            @else
                <span class="rounded-full bg-emerald-100 px-3.5 py-1 text-xs font-semibold text-emerald-800">
                    Semua sudah dibaca
                </span>
            @endif
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

    {{-- Daftar Notifikasi --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @forelse($notifications as $notification)
            @php
                $data = $notification->data;
                $isUnread = $notification->unread();
                $senderName = $data['sender_name'] ?? 'Pengguna';
                $snippet = $data['snippet'] ?? 'Pesan baru diterima.';
            @endphp
            <a href="{{ route('notifications.show', $notification->id) }}"
               class="group block border-b border-slate-100 border-l-4 p-4.5 transition last:border-b-0 hover:bg-slate-50/80 md:p-5
                      {{ $isUnread ? 'border-l-emerald-600 bg-emerald-50/40' : 'border-l-transparent' }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex min-w-0 items-start gap-3.5">
                        {{-- Avatar Pengirim --}}
                        <div class="relative flex h-12 w-12 shrink-0 items-center justify-center rounded-full border font-bold shadow-inner
                                    {{ $isUnread ? 'border-emerald-800 bg-emerald-800 text-white' : 'border-slate-200 bg-slate-100 text-slate-500' }}">
                            <span class="text-base uppercase">{{ mb_substr($senderName, 0, 1) }}</span>
                            {{-- Ikon pesan kecil di pojok avatar --}}
                            <span class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full border-2 border-white bg-indigo-600 text-white">
                                <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </span>
                        </div>

                        {{-- Isi Notifikasi --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-bold text-slate-900 group-hover:text-emerald-800">
                                    {{ $senderName }}
                                </span>
                                <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700">
                                    Pesan Baru
                                </span>
                                @if($isUnread)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-800">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        Baru
                                    </span>
                                @endif
                            </div>

                            <p class="mt-1 line-clamp-2 text-sm {{ $isUnread ? 'font-medium text-slate-700' : 'text-slate-500' }}">
                                &ldquo;{{ $snippet }}&rdquo;
                            </p>
                        </div>
                    </div>

                    {{-- Waktu & Aksi --}}
                    <div class="shrink-0 text-right">
                        <span class="whitespace-nowrap text-xs text-slate-400">
                            {{ $notification->created_at->diffForHumans() }}
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
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <h3 class="brand-font mt-3 text-lg font-bold text-slate-800">Belum Ada Notifikasi</h3>
                <p class="mt-1 text-sm text-slate-500">Pemberitahuan pesan dan aktivitas terbaru Anda akan muncul di sini.</p>
                <div class="mt-5 flex justify-center gap-3">
                    <a href="{{ route('chat.index') }}" class="rounded-lg bg-emerald-800 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-900">
                        Buka Pesan
                    </a>
                    <a href="{{ route('dashboard') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                        Ke Dashboard
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($notifications->count() >= 30)
        <p class="mt-4 text-center text-xs text-slate-400">Menampilkan 30 notifikasi terbaru.</p>
    @endif
</div>
@endsection
