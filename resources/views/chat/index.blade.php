@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pesan Masuk (Inbox)</h1>
        <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke Dashboard</a>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @forelse($conversations as $conversation)
            @php
                $otherUser = $conversation->getOtherParticipant(Auth::user());
                $lastMessage = $conversation->messages->first();
                $listingTitle = $conversation->serviceRequest?->title ?? $conversation->serviceOffer?->title ?? 'Listing Umum';
                $listingType = $conversation->serviceRequest ? 'Butuh Jasa' : ($conversation->serviceOffer ? 'Menyediakan Jasa' : 'Umum');
            @endphp
            <a href="{{ route('chat.show', $conversation) }}" class="block p-4 border-b border-gray-100 hover:bg-gray-50 transition">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 font-bold flex items-center justify-center uppercase">
                            {{ substr($otherUser?->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-gray-900">{{ $otherUser?->name ?? 'Pengguna' }}</span>
                                <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-600">{{ $listingType }}</span>
                                @if($conversation->isCompleted())
                                    <span class="text-xs px-2 py-0.5 rounded bg-green-100 text-green-700 font-medium">Selesai</span>
                                @endif
                            </div>
                            <p class="text-xs text-blue-600 font-medium">{{ $listingTitle }}</p>
                            <p class="text-sm text-gray-600 truncate max-w-md mt-1">
                                {{ $lastMessage?->body ?? 'Belum ada pesan.' }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-400">
                            {{ $lastMessage?->created_at?->diffForHumans() ?? $conversation->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </a>
        @empty
            <div class="p-8 text-center text-gray-500">
                <p>Belum ada percakapan.</p>
                <p class="text-sm mt-1">Mulai chat dari halaman Butuh Jasa atau Menyediakan Jasa.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $conversations->links() }}
    </div>
</div>
@endsection

