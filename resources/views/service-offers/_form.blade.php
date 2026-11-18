{{-- Frontend 2: form Menyediakan Jasa (dipakai create & edit). Nama field mengikuti validasi ServiceOfferController. --}}
@php
    $field = fn ($name, $pad = 'px-4') => 'w-full rounded-lg border bg-white ' . $pad . ' py-2.5 text-sm text-slate-800 placeholder-slate-400 outline-none focus:ring-2 '
        . ($errors->has($name) ? 'border-red-400 focus:ring-red-200' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-200');
@endphp

<div class="space-y-5">

    {{-- Kategori --}}
    @if(isset($categories) && $categories->isNotEmpty())
    <div>
        <label for="category_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Kategori</label>
        <select id="category_id" name="category_id" class="{{ $field('category_id') }}">
            <option value="">— Pilih Kategori —</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id', $serviceOffer->category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        @error('category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    @endif

    {{-- Judul --}}
    <div>
        <label for="title" class="mb-1.5 block text-sm font-semibold text-slate-700">Judul layanan <span class="text-red-500">*</span></label>
        <input id="title" type="text" name="title" maxlength="255" required
               value="{{ old('title', $serviceOffer->title ?? '') }}"
               placeholder="Contoh: Jasa desain logo profesional"
               class="{{ $field('title') }}">
        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Deskripsi --}}
    <div>
        <label for="description" class="mb-1.5 block text-sm font-semibold text-slate-700">Deskripsi <span class="text-red-500">*</span></label>
        <textarea id="description" name="description" rows="5" required minlength="20"
                  placeholder="Jelaskan layananmu: apa yang kamu tawarkan, pengalaman, dan hasil yang akan diterima pelanggan."
                  class="{{ $field('description') }}">{{ old('description', $serviceOffer->description ?? '') }}</textarea>
        <p class="mt-1 text-xs text-slate-400">Minimal 20 karakter.</p>
        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Harga + Tipe harga --}}
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="price" class="mb-1.5 block text-sm font-semibold text-slate-700">Harga</label>
            <div class="relative">
                <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                <input id="price" type="number" name="price" min="0" inputmode="numeric"
                       value="{{ old('price', $serviceOffer->price ?? '') }}"
                       placeholder="150000"
                       class="{{ $field('price', 'pr-4 pl-10') }}">
            </div>
            @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="price_type" class="mb-1.5 block text-sm font-semibold text-slate-700">Tipe harga</label>
            <select id="price_type" name="price_type" class="{{ $field('price_type') }}">
                <option value="">— Pilih —</option>
                @foreach (['fixed' => 'Harga tetap', 'hourly' => 'Per jam', 'negotiable' => 'Bisa nego'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('price_type', $serviceOffer->price_type ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('price_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Mode kerja + Lokasi --}}
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="work_mode" class="mb-1.5 block text-sm font-semibold text-slate-700">Mode kerja</label>
            <select id="work_mode" name="work_mode" class="{{ $field('work_mode') }}">
                <option value="">— Pilih —</option>
                @foreach (['online' => 'Online', 'onsite' => 'Onsite (tatap muka)', 'hybrid' => 'Hybrid'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('work_mode', $serviceOffer->work_mode ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('work_mode') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="location" class="mb-1.5 block text-sm font-semibold text-slate-700">Lokasi</label>
            <input id="location" type="text" name="location" maxlength="255"
                   value="{{ old('location', $serviceOffer->location ?? '') }}"
                   placeholder="Contoh: Pekanbaru"
                   class="{{ $field('location') }}">
            @error('location') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Portofolio --}}
    <div>
        <label for="portfolio_url" class="mb-1.5 block text-sm font-semibold text-slate-700">Link portofolio</label>
        <input id="portfolio_url" type="url" name="portfolio_url" maxlength="255"
               value="{{ old('portfolio_url', $serviceOffer->portfolio_url ?? '') }}"
               placeholder="https://..."
               class="{{ $field('portfolio_url') }}">
        @error('portfolio_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

</div>
