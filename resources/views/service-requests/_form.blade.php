{{-- Frontend 2: form Butuh Jasa (dipakai create & edit). Nama field mengikuti validasi ServiceRequestController. --}}
@php
    $field = fn ($name, $pad = 'px-4') => 'w-full rounded-lg border bg-white ' . $pad . ' py-2.5 text-sm text-slate-800 placeholder-slate-400 outline-none focus:ring-2 '
        . ($errors->has($name) ? 'border-red-400 focus:ring-red-200' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-200');
    $deadlineValue = old('deadline', isset($serviceRequest) && $serviceRequest->deadline ? substr((string) $serviceRequest->deadline, 0, 10) : '');
    $deadlineMin = isset($serviceRequest) ? null : now()->toDateString();
@endphp

<div class="space-y-5">

    {{-- Kategori --}}
    @if(isset($categories) && $categories->isNotEmpty())
    <div>
        <label for="category_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Kategori</label>
        <select id="category_id" name="category_id" class="{{ $field('category_id') }}">
            <option value="">— Pilih Kategori —</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id', $serviceRequest->category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        @error('category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    @endif

    {{-- Judul --}}
    <div>
        <label for="title" class="mb-1.5 block text-sm font-semibold text-slate-700">Judul permintaan <span class="text-red-500">*</span></label>
        <input id="title" type="text" name="title" maxlength="255" required
               value="{{ old('title', $serviceRequest->title ?? '') }}"
               placeholder="Contoh: Butuh desain logo untuk usaha kopi"
               class="{{ $field('title') }}">
        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Deskripsi --}}
    <div>
        <label for="description" class="mb-1.5 block text-sm font-semibold text-slate-700">Deskripsi <span class="text-red-500">*</span></label>
        <textarea id="description" name="description" rows="5" required minlength="20"
                  placeholder="Jelaskan kebutuhanmu: apa yang dikerjakan, hasil yang diharapkan, dan detail penting lainnya."
                  class="{{ $field('description') }}">{{ old('description', $serviceRequest->description ?? '') }}</textarea>
        <p class="mt-1 text-xs text-slate-400">Minimal 20 karakter.</p>
        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Budget --}}
    <div>
        <span class="mb-1.5 block text-sm font-semibold text-slate-700">Kisaran budget</span>
        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <div class="relative">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                    <input id="budget_min" type="number" name="budget_min" min="0" inputmode="numeric"
                           value="{{ old('budget_min', $serviceRequest->budget_min ?? '') }}"
                           placeholder="Minimum"
                           class="{{ $field('budget_min', 'pr-4 pl-10') }}">
                </div>
                @error('budget_min') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <div class="relative">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                    <input id="budget_max" type="number" name="budget_max" min="0" inputmode="numeric"
                           value="{{ old('budget_max', $serviceRequest->budget_max ?? '') }}"
                           placeholder="Maksimum"
                           class="{{ $field('budget_max', 'pr-4 pl-10') }}">
                </div>
                @error('budget_max') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Mode kerja + Lokasi --}}
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="work_mode" class="mb-1.5 block text-sm font-semibold text-slate-700">Mode kerja</label>
            <select id="work_mode" name="work_mode" class="{{ $field('work_mode') }}">
                <option value="">— Pilih —</option>
                @foreach (['online' => 'Online', 'onsite' => 'Onsite (tatap muka)', 'hybrid' => 'Hybrid'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('work_mode', $serviceRequest->work_mode ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('work_mode') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="location" class="mb-1.5 block text-sm font-semibold text-slate-700">Lokasi</label>
            <input id="location" type="text" name="location" maxlength="255"
                   value="{{ old('location', $serviceRequest->location ?? '') }}"
                   placeholder="Contoh: Pekanbaru"
                   class="{{ $field('location') }}">
            @error('location') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Deadline --}}
    <div>
        <label for="deadline" class="mb-1.5 block text-sm font-semibold text-slate-700">Deadline</label>
        <input id="deadline" type="date" name="deadline"
               value="{{ $deadlineValue }}"
               @if ($deadlineMin) min="{{ $deadlineMin }}" @endif
               class="{{ $field('deadline') }} sm:max-w-xs">
        @error('deadline') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

</div>
