{{-- Frontend 2: Halaman Ruang Obrolan (Chat Room).
     Mengikuti desain layouts/marketplace, polling otomatis 3 detik Vanilla JS, dan responsif. --}}
@extends('layouts.marketplace')

@section('title', 'Chat — ' . ($conversation->getOtherParticipant(Auth::user())?->name ?? 'Percakapan'))

@section('content')
@php
    $user = Auth::user();
    $otherUser = $conversation->getOtherParticipant($user);
    $listing = $conversation->serviceRequest ?? $conversation->serviceOffer;
    $isCustomer = $conversation->customer_id === $user->id;
    $isCompleted = $conversation->isCompleted();
    $otherProfileUrl = $otherUser ? route('profile.show', $otherUser) : '#';
@endphp

<div class="mx-auto max-w-4xl px-4 py-6 md:py-8">

    {{-- Kartu Utama Obrolan --}}
    <div class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        {{-- Header Ruang Chat --}}
        <div class="flex flex-col gap-4 border-b border-slate-200 bg-slate-50/60 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('chat.index') }}" class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-200/60 hover:text-slate-800 transition" title="Kembali ke Inbox">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>

                {{-- Avatar Lawan Bicara --}}
                <a href="{{ $otherProfileUrl }}" class="relative flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-emerald-800 text-sm font-bold text-white shadow-inner hover:opacity-90 transition">
                    @if ($otherUser?->avatar)
                        <img src="{{ asset('storage/' . $otherUser->avatar) }}" alt="{{ $otherUser->name }}" class="h-full w-full object-cover">
                    @else
                        <span class="uppercase">{{ mb_substr($otherUser?->name ?? 'P', 0, 1) }}</span>
                    @endif
                </a>

                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <a href="{{ $otherProfileUrl }}" class="truncate font-bold text-slate-900 hover:text-emerald-800 hover:underline">
                            {{ $otherUser?->name ?? 'Pengguna' }}
                        </a>
                        <span class="rounded-full bg-slate-200/70 px-2 py-0.5 text-[10px] font-semibold text-slate-700 capitalize">
                            {{ $otherUser?->role ?? '-' }}
                        </span>
                    </div>

                    @if($listing)
                        <p class="truncate text-xs text-slate-500">
                            Listing: 
                            <span class="font-medium text-emerald-800">
                                {{ $listing->title }}
                            </span>
                        </p>
                    @endif
                </div>
            </div>

            {{-- Tombol Status & Selesai --}}
            <div class="flex items-center gap-2 self-end sm:self-center">
                @if($isCompleted)
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                        Pekerjaan Selesai
                    </span>
                @elseif($isCustomer)
                    <form action="{{ route('chat.complete', $conversation) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menandai pekerjaan ini selesai? Setelah selesai, Anda dapat memberikan ulasan.')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-800 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-900 transition">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Tandai Selesai
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="border-b border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs font-medium text-emerald-800" role="status">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="border-b border-red-200 bg-red-50 px-4 py-2.5 text-xs font-medium text-red-700" role="alert">
                {{ session('error') }}
            </div>
        @endif

        {{-- Form Ulasan Terintegrasi --}}
        @include('chat._review-form')

        {{-- Area Pesan Obrolan --}}
        <div id="messages-container" class="flex h-[460px] flex-col gap-3 overflow-y-auto bg-[#fafafa] p-4 md:p-6">
            @forelse($conversation->messages as $msg)
                @php $isMe = $msg->sender_id === $user->id; @endphp
                <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }} message-item" data-id="{{ $msg->id }}">
                    <div class="max-w-[80%] rounded-2xl px-4 py-2.5 text-sm shadow-sm md:max-w-[70%] {{ $isMe ? 'rounded-br-xs bg-emerald-800 text-white' : 'rounded-bl-xs border border-slate-200 bg-white text-slate-800' }}">
                        <p class="whitespace-pre-wrap break-words leading-relaxed">{{ $msg->body }}</p>
                    </div>
                    <span class="mt-1 px-1 text-[10px] text-slate-400">
                        {{ $msg->created_at ? $msg->created_at->format('H:i') : '' }}
                        @if($isMe && $msg->read_at)
                            &bull; <span class="text-emerald-700 font-medium">dibaca</span>
                        @endif
                    </span>
                </div>
            @empty
                <div id="no-messages-placeholder" class="m-auto text-center py-10">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                    <p class="mt-2 text-xs font-semibold text-slate-600">Belum ada percakapan</p>
                    <p class="mt-0.5 text-[11px] text-slate-400">Kirim pesan pertama untuk mulai mendiskusikan pekerjaan.</p>
                </div>
            @endforelse
        </div>

        {{-- Form Kirim Pesan --}}
        <form id="message-form" action="{{ route('chat.send', $conversation) }}" method="POST" class="border-t border-slate-200 bg-white p-3 md:p-4">
            @csrf
            <div class="flex items-center gap-2">
                <input 
                    type="text" 
                    name="body" 
                    id="message-input"
                    required
                    autocomplete="off"
                    placeholder="Tulis pesan Anda di sini..." 
                    class="flex-1 rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-100"
                />
                <button 
                    type="submit" 
                    id="send-btn"
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-emerald-800 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-emerald-900 transition"
                >
                    <span>Kirim</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </div>
        </form>

    </div>
</div>

{{-- Skrip Polling & AJAX Chat --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('messages-container');
    const form = document.getElementById('message-form');
    const input = document.getElementById('message-input');
    const pollUrl = "{{ route('chat.messages', $conversation) }}";

    function scrollToBottom() {
        container.scrollTop = container.scrollHeight;
    }
    scrollToBottom();

    function getLastMessageId() {
        const items = document.querySelectorAll('.message-item');
        if (items.length === 0) return 0;
        return parseInt(items[items.length - 1].getAttribute('data-id')) || 0;
    }

    function appendMessage(msg) {
        const placeholder = document.getElementById('no-messages-placeholder');
        if (placeholder) placeholder.remove();

        // Cek jika ID pesan sudah ada di DOM
        if (document.querySelector(`.message-item[data-id="${msg.id}"]`)) return;

        const isMe = msg.is_sender;
        const msgDiv = document.createElement('div');
        msgDiv.className = `flex flex-col ${isMe ? 'items-end' : 'items-start'} message-item`;
        msgDiv.setAttribute('data-id', msg.id);

        const bubbleClasses = isMe 
            ? 'rounded-br-xs bg-emerald-800 text-white' 
            : 'rounded-bl-xs border border-slate-200 bg-white text-slate-800';

        msgDiv.innerHTML = `
            <div class="max-w-[80%] rounded-2xl px-4 py-2.5 text-sm shadow-sm md:max-w-[70%] ${bubbleClasses}">
                <p class="whitespace-pre-wrap break-words leading-relaxed">${escapeHtml(msg.body)}</p>
            </div>
            <span class="mt-1 px-1 text-[10px] text-slate-400">
                ${msg.created_at || ''}
            </span>
        `;
        container.appendChild(msgDiv);
        scrollToBottom();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Ajax Form Submit
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const bodyText = input.value.trim();
        if (!bodyText) return;

        const csrfToken = document.querySelector('input[name="_token"]').value;

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ body: bodyText })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.message) {
                appendMessage(data.message);
                input.value = '';
            } else if (data.errors) {
                alert(Object.values(data.errors).flat().join("\n"));
            }
        })
        .catch(err => console.error('Send message error:', err));
    });

    // Polling setiap 3 detik
    setInterval(function () {
        const afterId = getLastMessageId();
        fetch(`${pollUrl}?after=${afterId}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => appendMessage(msg));
            }
        })
        .catch(err => console.error('Polling error:', err));
    }, 3000);
});
</script>
@endsection
