@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Notifikasi</h1>
            <p class="text-sm text-gray-500">Pemberitahuan pesan dan aktivitas terbaru Anda</p>
        </div>
        <div class="flex items-center gap-3">
            @if($unreadCount > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs font-semibold px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition">
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Dashboard</a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden divide-y divide-gray-100">
        @forelse($notifications as $notification)
            @php
                $data = $notification->data;
                $isUnread = $notification->unread();
                $senderName = $data['sender_name'] ?? 'Pengguna';
                $snippet = $data['snippet'] ?? 'Pesan baru diterima.';
            @endphp
            <a href="{{ route('notifications.show', $notification->id) }}" class="block p-4 hover:bg-gray-50 transition {{ $isUnread ? 'bg-blue-50/40' : '' }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-full {{ $isUnread ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} font-bold flex items-center justify-center text-xs uppercase flex-shrink-0 mt-0.5">
                            {{ substr($senderName, 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-gray-900 text-sm">{{ $senderName }}</span>
                                @if($isUnread)
                                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-700 mt-1 line-clamp-2">
                                "{{ $snippet }}"
                            </p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 whitespace-nowrap">
                        {{ $notification->created_at->diffForHumans() }}
                    </span>
                </div>
            </a>
        @empty
            <div class="p-8 text-center text-gray-500">
                <p>Tidak ada notifikasi saat ini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

