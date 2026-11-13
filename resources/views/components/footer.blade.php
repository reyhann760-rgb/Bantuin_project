<footer class="mt-16 border-t border-slate-200 bg-white">
    <div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 md:grid-cols-3">
        <div>
            <p class="text-xl font-bold text-indigo-600">Bantuin</p>
            <p class="mt-2 text-sm text-slate-500">Platform yang mempertemukan orang yang butuh jasa dengan penyedia jasa terpercaya.</p>
        </div>
        <div>
            <p class="text-sm font-semibold text-slate-800">Jelajahi</p>
            <ul class="mt-3 space-y-2 text-sm text-slate-500">
                <li><a href="{{ route('home') }}" class="hover:text-indigo-600">Beranda</a></li>
                <li><a href="{{ route('butuh.index') }}" class="hover:text-indigo-600">Butuh Jasa</a></li>
                <li><a href="{{ route('sedia.index') }}" class="hover:text-indigo-600">Menyediakan Jasa</a></li>
            </ul>
        </div>
        <div>
            <p class="text-sm font-semibold text-slate-800">Kontak</p>
            <ul class="mt-3 space-y-2 text-sm text-slate-500">
                <li>halo@bantuin.test</li>
                <li>Pekanbaru, Riau</li>
            </ul>
        </div>
    </div>
    <div class="border-t border-slate-100 py-4 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} Bantuin. Dibuat oleh kelompok kami.
    </div>
</footer>
