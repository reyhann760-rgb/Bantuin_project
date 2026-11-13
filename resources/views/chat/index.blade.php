@extends('layouts.app')
@section('title', 'Chat')

@php
    $conversations = $conversations ?? collect();
    $messages      = $messages ?? collect();
    $activeConversation = $activeConversation ?? null;
@endphp

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8">
    <h1 class="mb-4 text-2xl font-bold text-gray-900">Chat</h1>

    <div class="grid h-[70vh] overflow-hidden rounded-xl border border-gray-200 bg-white md:grid-cols-3">
        {{-- Daftar percakapan --}}
        <aside class="overflow-y-auto border-b border-gray-200 md:border-b-0 md:border-r">
            @forelse ($conversations as $c)
                <a href="{{ route('chat.index', ['conversation' => $c->id]) }}"
                   class="block border-b border-gray-100 px-4 py-3 hover:bg-gray-50 {{ optional($activeConversation)->id === $c->id ? 'bg-indigo-50' : '' }}">
                    <p class="text-sm font-medium text-gray-900">{{ $c->title ?? 'Percakapan #'.$c->id }}</p>
                    <p class="truncate text-xs text-gray-500">{{ $c->last_message ?? 'Belum ada pesan' }}</p>
                </a>
            @empty
                <p class="px-4 py-6 text-sm text-gray-500">Belum ada percakapan. Mulai chat dari halaman detail jasa.</p>
            @endforelse
        </aside>

        {{-- Isi percakapan --}}
        <section class="flex flex-col md:col-span-2">
            <div class="flex-1 space-y-3 overflow-y-auto p-4">
                @forelse ($messages as $m)
                    @php $mine = $m->sender_id === auth()->id(); @endphp
                    <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[75%] rounded-2xl px-4 py-2 text-sm {{ $mine ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800' }}">
                            {{ $m->body }}
                        </div>
                    </div>
                @empty
                    <p class="mt-10 text-center text-sm text-gray-500">Pilih percakapan untuk mulai mengirim pesan.</p>
                @endforelse
            </div>

            @if ($activeConversation)
            <form method="POST" action="{{ route('chat.store', $activeConversation->id) }}" class="flex gap-2 border-t border-gray-200 p-3">
                @csrf
                <input name="body" required autocomplete="off" placeholder="Tulis pesan"
                       class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Kirim</button>
            </form>
            @endif
        </section>
    </div>
</div>
@endsection
