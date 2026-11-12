{{-- PLACEHOLDER dari Backend 2: tampilan polos. Frontend silakan ganti isi file ini (nama file, variabel, nama field, dan nama route jangan diubah). --}}

<div class="form-group">
    <label>Judul</label>
    <input type="text" name="title" value="{{ old('title', $serviceOffer->title ?? '') }}" placeholder="Contoh: Jasa desain logo">
</div>

<div class="form-group">
    <label>Deskripsi (minimal 20 karakter)</label>
    <textarea name="description" rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 7px; font-size: 14px; font-family: inherit;">{{ old('description', $serviceOffer->description ?? '') }}</textarea>
</div>

<div class="form-group">
    <label>Harga</label>
    <input type="number" name="price" min="0" value="{{ old('price', $serviceOffer->price ?? '') }}" placeholder="Contoh: 150000">
</div>

<div class="form-group">
    <label>Tipe Harga</label>
    <select name="price_type">
        <option value="">- Pilih -</option>
        @foreach(['fixed', 'hourly', 'negotiable'] as $type)
            <option value="{{ $type }}" @selected(old('price_type', $serviceOffer->price_type ?? '') === $type)>{{ ucfirst($type) }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Mode Kerja</label>
    <select name="work_mode">
        <option value="">- Pilih -</option>
        @foreach(['online', 'onsite', 'hybrid'] as $mode)
            <option value="{{ $mode }}" @selected(old('work_mode', $serviceOffer->work_mode ?? '') === $mode)>{{ ucfirst($mode) }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Lokasi</label>
    <input type="text" name="location" value="{{ old('location', $serviceOffer->location ?? '') }}" placeholder="Contoh: Pekanbaru">
</div>

<div class="form-group">
    <label>Link Portofolio</label>
    <input type="text" name="portfolio_url" value="{{ old('portfolio_url', $serviceOffer->portfolio_url ?? '') }}" placeholder="https://...">
</div>