@php
    $links = [
        ['Beranda', route('home'), request()->routeIs('home')],
        ['Butuh Jasa', route('butuh.index'), request()->routeIs('butuh.*')],
        ['Menyediakan Jasa', route('sedia.index'), request()->routeIs('sedia.*')],
    ];
    $login = Route::has('login') ? route('login') : '#';
    $register = Route::has('register') ? route('register') : '#';
@endphp
<header class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur">
    <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
        <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-600">Bantuin</a>

        <ul class="hidden items-center gap-6 md:flex">
            @foreach ($links as [$label, $url, $active])
                <li><a href="{{ $url }}" class="text-sm font-medium {{ $active ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }}">{{ $label }}</a></li>
            @endforeach
        </ul>

        <div class="hidden items-center gap-3 md:flex">
            <a href="{{ $login }}" class="text-sm font-medium text-slate-700 hover:text-indigo-600">Masuk</a>
            <a href="{{ $register }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Daftar</a>
        </div>

        <button type="button" class="rounded-lg p-2 text-slate-700 hover:bg-slate-100 md:hidden" aria-label="Menu"
                onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </nav>

    <div id="mobile-menu" class="hidden border-t border-slate-200 bg-white md:hidden">
        <ul class="space-y-1 px-4 py-3">
            @foreach ($links as [$label, $url, $active])
                <li><a href="{{ $url }}" class="block rounded-lg px-3 py-2 text-sm font-medium {{ $active ? 'bg-indigo-50 text-indigo-600' : 'text-slate-700' }}">{{ $label }}</a></li>
            @endforeach
            <li class="flex gap-2 pt-2">
                <a href="{{ $login }}" class="flex-1 rounded-lg border border-slate-300 py-2 text-center text-sm font-medium">Masuk</a>
                <a href="{{ $register }}" class="flex-1 rounded-lg bg-indigo-600 py-2 text-center text-sm font-semibold text-white">Daftar</a>
            </li>
        </ul>
    </div>
</header>
