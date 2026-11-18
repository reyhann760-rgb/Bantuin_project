@if (session('success'))
    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-700">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">
        {{ session('error') }}
    </div>
@endif
