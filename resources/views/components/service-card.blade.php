@props(['item', 'type' => 'butuh'])
@php
    $isButuh = $type === 'butuh';
    $url = route($isButuh ? 'butuh.show' : 'sedia.show', $item->id);
    $badge = $isButuh ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700';
@endphp
<a href="{{ $url }}" class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
    <div class="flex items-center justify-between">
        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-600">{{ $item->kategori }}</span>
        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $badge }}">{{ $item->status }}</span>
    </div>
    <h3 class="mt-3 text-base font-semibold text-slate-900 group-hover:text-indigo-600">{{ $item->judul }}</h3>
    <p class="mt-2 line-clamp-2 text-sm text-slate-500">{{ $item->deskripsi }}</p>
    <div class="mt-auto flex items-end justify-between pt-4">
        <div>
            <p class="text-xs text-slate-400">{{ $isButuh ? 'Anggaran' : 'Mulai dari' }}</p>
            <p class="font-bold text-slate-900">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
        </div>
        <p class="text-xs text-slate-500">{{ $item->lokasi }} &middot; {{ $item->pengguna }}</p>
    </div>
</a>
