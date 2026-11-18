{{-- Frontend 2: Halaman Manajemen Kategori Admin.
     Mengikuti desain layouts/marketplace, palet warna Bantuin, responsif, dan modal edit modern. --}}
@extends('layouts.marketplace')

@section('title', 'Manajemen Kategori — Admin')

@section('content')
{{-- Header Halaman --}}
<section class="border-b border-slate-200 bg-[#f6f7f2]">
    <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-8 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-800 hover:text-emerald-900 hover:underline">
                &larr; Kembali ke Dashboard
            </a>
            <div class="mt-2 flex items-center gap-3">
                <h1 class="brand-font text-2xl font-extrabold text-slate-900 md:text-3xl">Manajemen Kategori</h1>
                <span class="rounded-full bg-slate-900 px-3 py-0.5 text-xs font-semibold text-white">Panel Admin</span>
            </div>
            <p class="mt-1 text-sm text-slate-500">Kelola kategori layanan untuk kebutuhan dan penawaran jasa pada marketplace.</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="rounded-full bg-emerald-100 px-3.5 py-1 text-xs font-semibold text-emerald-800">
                Total: {{ $categories->total() }} Kategori
            </span>
        </div>
    </div>
</section>

<div class="mx-auto max-w-6xl px-4 py-8">

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm" role="status">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm">
            <p class="font-semibold">Terjadi kesalahan validasi:</p>
            <ul class="mt-1 list-disc space-y-0.5 pl-5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-8 lg:grid-cols-3">

        {{-- Kolom Kiri: Form Tambah Kategori --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="border-b border-slate-100 pb-4">
                    <h2 class="brand-font text-lg font-bold text-slate-900">Tambah Kategori</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Buat klasifikasi baru untuk listing jasa.</p>
                </div>

                <form action="{{ route('admin.categories.store') }}" method="POST" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label for="create_name" class="mb-1.5 block text-xs font-semibold text-slate-700">Nama Kategori <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            name="name" 
                            id="create_name" 
                            required 
                            maxlength="100"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Elektronik & Komputer" 
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 placeholder-slate-400 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        />
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="create_slug" class="mb-1.5 block text-xs font-semibold text-slate-700">Slug URL (Opsional)</label>
                        <input 
                            type="text" 
                            name="slug" 
                            id="create_slug" 
                            maxlength="120"
                            value="{{ old('slug') }}"
                            placeholder="otomatis dari nama jika kosong" 
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 placeholder-slate-400 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        />
                        <p class="mt-1 text-[11px] text-slate-400">Hanya huruf kecil, angka, dan tanda hubung (-).</p>
                        @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button 
                        type="submit" 
                        class="w-full rounded-lg bg-emerald-800 px-4 py-2.5 text-center text-sm font-bold text-white shadow-sm hover:bg-emerald-900 transition"
                    >
                        + Simpan Kategori
                    </button>
                </form>
            </div>
        </div>

        {{-- Kolom Kanan: Tabel Daftar Kategori --}}
        <div class="lg:col-span-2">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-6 py-4 flex items-center justify-between">
                    <h2 class="brand-font text-lg font-bold text-slate-900">Daftar Kategori</h2>
                    <span class="text-xs text-slate-400">Diurutkan berdasarkan yang terbaru</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/80 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200/80">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 font-bold">Kategori</th>
                                <th scope="col" class="px-6 py-3.5 font-bold">Slug</th>
                                <th scope="col" class="px-6 py-3.5 font-bold text-center">Terkait</th>
                                <th scope="col" class="px-6 py-3.5 font-bold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($categories as $cat)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900">{{ $cat->name }}</div>
                                        <div class="text-[11px] text-slate-400">Dibuat {{ $cat->created_at ? $cat->created_at->format('d M Y') : '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <code class="rounded bg-slate-100 px-2 py-0.5 text-xs text-slate-600 font-mono">{{ $cat->slug }}</code>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex items-center gap-1.5 text-xs">
                                            <span class="rounded bg-indigo-50 px-2 py-0.5 font-semibold text-indigo-700" title="Butuh Jasa">
                                                {{ $cat->service_requests_count }} Butuh
                                            </span>
                                            <span class="rounded bg-emerald-50 px-2 py-0.5 font-semibold text-emerald-700" title="Menyediakan Jasa">
                                                {{ $cat->service_offers_count }} Sedia
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            {{-- Tombol Edit (Memicu Modal JS) --}}
                                            <button 
                                                type="button" 
                                                onclick="openEditModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->slug) }}')"
                                                class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 shadow-xs hover:border-slate-300 hover:bg-slate-50 transition"
                                                title="Ubah Kategori"
                                            >
                                                Edit
                                            </button>

                                            {{-- Form Hapus --}}
                                            <form 
                                                action="{{ route('admin.categories.destroy', $cat) }}" 
                                                method="POST" 
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori &quot;{{ $cat->name }}&quot;? Seluruh listing terkait juga dapat terhapus secara cascade!')"
                                                class="inline"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="rounded-lg border border-red-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-red-600 shadow-xs hover:bg-red-50 transition"
                                                    title="Hapus Kategori"
                                                >
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                        <p class="font-medium">Belum ada kategori yang terdaftar.</p>
                                        <p class="mt-1 text-xs">Gunakan formulir di samping untuk menambahkan kategori baru.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($categories->hasPages())
                    <div class="border-t border-slate-100 p-4">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- Modal Edit Kategori (Vanilla JS) --}}
<div id="edit-category-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4 backdrop-blur-xs">
    <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="brand-font text-lg font-bold text-slate-900">Ubah Kategori</h3>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form id="edit-category-form" method="POST" class="mt-4 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="edit_name" class="mb-1.5 block text-xs font-semibold text-slate-700">Nama Kategori <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    name="name" 
                    id="edit_name" 
                    required 
                    maxlength="100"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 placeholder-slate-400 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                />
            </div>

            <div>
                <label for="edit_slug" class="mb-1.5 block text-xs font-semibold text-slate-700">Slug URL</label>
                <input 
                    type="text" 
                    name="slug" 
                    id="edit_slug" 
                    maxlength="120"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 placeholder-slate-400 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                />
                <p class="mt-1 text-[11px] text-slate-400">Kosongkan jika ingin dibuat otomatis mengikuti nama.</p>
            </div>

            <div class="mt-6 flex justify-end gap-2 pt-2">
                <button 
                    type="button" 
                    onclick="closeEditModal()" 
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="rounded-lg bg-emerald-800 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-900 transition"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, name, slug) {
    const modal = document.getElementById('edit-category-modal');
    const form = document.getElementById('edit-category-form');
    const nameInput = document.getElementById('edit_name');
    const slugInput = document.getElementById('edit_slug');

    form.action = `/admin/kategori/${id}`;
    nameInput.value = name;
    slugInput.value = slug;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditModal() {
    const modal = document.getElementById('edit-category-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection

