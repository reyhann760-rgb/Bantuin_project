{{-- Admin: kelola (hapus) jasa satu per satu. --}}
@extends('layouts.marketplace')

@section('title', 'Kelola Jasa — Admin')

@section('content')
@php
    $isRequest = $tipe === 'butuh';
    $statusLabel = [
        'open' => ['Terbuka', 'bg-emerald-100 text-emerald-700'],
        'in_progress' => ['Berjalan', 'bg-amber-100 text-amber-700'],
        'completed' => ['Selesai', 'bg-slate-100 text-slate-600'],
        'cancelled' => ['Dibatalkan', 'bg-red-100 text-red-700'],
        'active' => ['Aktif', 'bg-emerald-100 text-emerald-700'],
        'inactive' => ['Nonaktif', 'bg-slate-100 text-slate-600'],
    ];
@endphp

<section class="border-b border-slate-200 bg-[#f6f7f2]">
    <div class="mx-auto max-w-6xl px-4 py-8">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-800 hover:underline">&larr; Kembali ke Dashboard</a>
        <div class="mt-2 flex items-center gap-3">
            <h1 class="brand-font text-2xl font-extrabold text-slate-900 md:text-3xl">Kelola Jasa</h1>
            <span class="rounded-full bg-slate-900 px-3 py-0.5 text-xs font-semibold text-white">Panel Admin</span>
        </div>
        <p class="mt-1 text-sm text-slate-500">Hapus jasa satu per satu. Menghapus sebuah jasa tidak memengaruhi kategorinya.</p>
    </div>
</section>

<div class="mx-auto max-w-6xl space-y-6 px-4 py-8">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="status">{{ session('success') }}</div>
    @endif

    {{-- Tab + pencarian --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="inline-flex rounded-lg border border-slate-200 bg-white p-1 text-sm font-semibold">
            <a href="{{ route('admin.services.index', ['tipe' => 'butuh']) }}"
               class="rounded-md px-4 py-2 {{ $isRequest ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50' }}">Butuh Jasa ({{ $countRequests }})</a>
            <a href="{{ route('admin.services.index', ['tipe' => 'sedia']) }}"
               class="rounded-md px-4 py-2 {{ ! $isRequest ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50' }}">Menyediakan Jasa ({{ $countOffers }})</a>
        </div>
        <form method="GET" action="{{ route('admin.services.index') }}" class="flex gap-2">
            <input type="hidden" name="tipe" value="{{ $tipe }}">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari judul jasa..."
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm md:w-64">
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Cari</button>
        </form>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3.5 font-bold">Judul</th>
                    <th class="px-5 py-3.5 font-bold">{{ $isRequest ? 'Customer' : 'Penyedia' }}</th>
                    <th class="px-5 py-3.5 font-bold">Kategori</th>
                    <th class="px-5 py-3.5 font-bold">Status</th>
                    <th class="px-5 py-3.5 text-right font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($items as $item)
                    @php
                        $owner = $isRequest ? $item->customer : $item->provider;
                        $badge = $statusLabel[$item->status] ?? [ucfirst($item->status), 'bg-slate-100 text-slate-600'];
                        $showUrl = $isRequest ? route('service-requests.show', $item) : route('service-offers.show', $item);
                        $deleteUrl = $isRequest ? route('admin.services.requests.destroy', $item) : route('admin.services.offers.destroy', $item);
                    @endphp
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-5 py-4">
                            <a href="{{ $showUrl }}" class="font-bold text-slate-900 hover:text-indigo-600">{{ $item->title }}</a>
                            <div class="text-[11px] text-slate-400">Dibuat {{ $item->created_at?->format('d M Y') }}</div>
                        </td>
                        <td class="px-5 py-4 text-slate-700">{{ $owner->name ?? '-' }}</td>
                        <td class="px-5 py-4 text-slate-700">{{ $item->category->name ?? '-' }}</td>
                        <td class="px-5 py-4"><span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $badge[1] }}">{{ $badge[0] }}</span></td>
                        <td class="px-5 py-4 text-right">
                            <form action="{{ $deleteUrl }}" method="POST" class="inline"
                                  onsubmit="return confirm('Hapus jasa &quot;{{ e($item->title) }}&quot;? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-slate-400">Tidak ada jasa{{ $search !== '' ? ' yang cocok dengan pencarian' : '' }}.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $items->links() }}</div>
</div>
@endsection
