@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    {{-- Header Percakapan --}}
    @php
        $user = Auth::user();
        $otherUser = $conversation->getOtherParticipant($user);
        $listing = $conversation->serviceRequest ?? $conversation->serviceOffer;
        $isCustomer = $conversation->customer_id === $user->id;
    @endphp

    <div class="bg-white rounded-t-xl border border-b-0 border-gray-200 p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('chat.index') }}" class="text-gray-400 hover:text-gray-600 mr-1">&larr;</a>
            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 font-bold flex items-center justify-center uppercase">
                {{ substr($otherUser?->name ?? 'U', 0, 1) }}
            </div>
            <div>
                <h2 class="font-bold text-gray-900 leading-tight">{{ $otherUser?->name ?? 'Pengguna' }}</h2>
                <p class="text-xs text-gray-500">
                    Role: <span class="capitalize">{{ $otherUser?->role ?? '-' }}</span>
                    @if($listing)
                        &bull; Listing: <span class="font-medium text-blue-600">{{ $listing->title }}</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($conversation->isCompleted())
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                    &check; Selesai
                </span>
            @elseif($isCustomer)
                <form action="{{ route('chat.complete', $conversation) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menandai pekerjaan ini selesai?')">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-green-600 hover:bg-green-700 text-white rounded-lg transition shadow-sm">
                        Tandai Selesai
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="p-3 text-sm text-green-700 bg-green-100 border-x border-gray-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="p-3 text-sm text-blue-700 bg-blue-100 border-x border-gray-200">
            {{ session('info') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-3 text-sm text-red-700 bg-red-100 border-x border-gray-200">
            {{ session('error') }}
        </div>
    @endif

    {{-- Area Pesan --}}
    <div id="messages-container" class="bg-gray-50 border border-gray-200 p-4 h-[420px] overflow-y-auto flex flex-col gap-3">
        @forelse($conversation->messages as $msg)
            @php $isMe = $msg->sender_id === $user->id; @endphp
            <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }} message-item" data-id="{{ $msg->id }}">
                <div class="max-w-[75%] rounded-2xl px-4 py-2 text-sm shadow-sm {{ $isMe ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-gray-800 border border-gray-200 rounded-bl-none' }}">
                    <p class="whitespace-pre-wrap break-words">{{ $msg->body }}</p>
                </div>
                <span class="text-[10px] text-gray-400 mt-1 px-1">
                    {{ $msg->created_at->format('H:i') }}
                    @if($isMe && $msg->read_at)
                        &bull; dibaca
                    @endif
                </span>
            </div>
        @empty
            <div id="no-messages-placeholder" class="m-auto text-center text-gray-400 text-sm">
                Belum ada pesan. Kirim pesan pertama untuk memulai percakapan.
            </div>
        @endforelse
    </div>

    {{-- Form Kirim Pesan --}}
    <form id="message-form" action="{{ route('chat.send', $conversation) }}" method="POST" class="bg-white rounded-b-xl border border-t-0 border-gray-200 p-3 flex gap-2">
        @csrf
        <input 
            type="text" 
            name="body" 
            id="message-input"
            required
            autocomplete="off"
            placeholder="Tulis pesan..." 
            class="flex-1 px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
        <button 
            type="submit" 
            id="send-btn"
            class="px-5 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition"
        >
            Kirim
        </button>
    </form>
</div>

{{-- Skrip Polling Pesan 3 Detik (Vanilla JS per PRD & Ketentuan Tanpa Node/Build) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('messages-container');
    const form = document.getElementById('message-form');
    const input = document.getElementById('message-input');
    const pollUrl = "{{ route('chat.messages', $conversation) }}";
    const currentUserId = {{ Auth::id() }};

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

        // Hindari duplikasi jika pesan sudah ada
        if (document.querySelector(`.message-item[data-id="${msg.id}"]`)) return;

        const isMe = msg.is_sender;
        const msgDiv = document.createElement('div');
        msgDiv.className = `flex flex-col ${isMe ? 'items-end' : 'items-start'} message-item`;
        msgDiv.setAttribute('data-id', msg.id);

        msgDiv.innerHTML = `
            <div class="max-w-[75%] rounded-2xl px-4 py-2 text-sm shadow-sm ${isMe ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-gray-800 border border-gray-200 rounded-bl-none'}">
                <p class="whitespace-pre-wrap break-words">${escapeHtml(msg.body)}</p>
            </div>
            <span class="text-[10px] text-gray-400 mt-1 px-1">
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

