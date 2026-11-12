{{-- PLACEHOLDER dari Backend 2: tampilan polos. Frontend silakan ganti isi file ini (nama file, variabel, nama field, dan nama route jangan diubah). --}}

<div class="form-group">
    <label>Judul</label>
    <input type="text" name="title" value="{{ old('title', $serviceRequest->title ?? '') }}" placeholder="Contoh: Butuh desain logo">
</div>

<div class="form-group">
    <label>Deskripsi (minimal 20 karakter)</label>
    <textarea name="description" rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 7px; font-size: 14px; font-family: inherit;">{{ old('description', $serviceRequest->description ?? '') }}</textarea>
</div>

<div class="form-group">
    <label>Budget Minimum</label>
    <input type="number" name="budget_min" min="0" value="{{ old('budget_min', $serviceRequest->budget_min ?? '') }}" placeholder="Contoh: 100000">
</div>

<div class="form-group">
    <label>Budget Maksimum</label>
    <input type="number" name="budget_max" min="0" value="{{ old('budget_max', $serviceRequest->budget_max ?? '') }}" placeholder="Contoh: 500000">
</div>

<div class="form-group">
    <label>Mode Kerja</label>
    <select name="work_mode">
        <option value="">- Pilih -</option>
        @foreach(['online', 'onsite', 'hybrid'] as $mode)
            <option value="{{ $mode }}" @selected(old('work_mode', $serviceRequest->work_mode ?? '') === $mode)>{{ ucfirst($mode) }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Lokasi</label>
    <input type="text" name="location" value="{{ old('location', $serviceRequest->location ?? '') }}" placeholder="Contoh: Pekanbaru">
</div>

<div class="form-group">
    <label>Deadline</label>
    <input type="date" name="deadline" value="{{ old('deadline', $serviceRequest->deadline ?? '') }}">
</div>