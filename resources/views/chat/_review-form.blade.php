{{-- Frontend 2: Komponen Form dan Info Ulasan pada Ruang Chat.
     Mengikuti tema layouts/marketplace, konsisten dengan rating badge Bantuin. --}}
@php
    $user = Auth::user();
    $isCustomer = $conversation->customer_id === $user->id;
    $hasReviewed = $conversation->hasReviewedBy($user);
    $existingReview = $conversation->reviews->first();
@endphp

@if($conversation->isCompleted())
    <div class="border-b border-amber-200 bg-amber-50/70 p-4 transition">
        @if($hasReviewed)
            <div class="flex items-start gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-100 text-lg text-amber-600">
                    &#9733;
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-slate-900 text-sm">Ulasan Telah Diberikan</h3>
                        <span class="text-[11px] text-slate-400">
                            {{ $existingReview?->created_at?->format('d M Y, H:i') }}
                        </span>
                    </div>

                    <div class="mt-1 flex items-center gap-1 text-amber-500 text-sm">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= ($existingReview?->rating ?? 0))
                                <span>&#9733;</span>
                            @else
                                <span class="text-slate-300">&#9733;</span>
                            @endif
                        @endfor
                        <span class="ml-1 text-xs font-bold text-slate-700">({{ $existingReview?->rating ?? 0 }}/5)</span>
                    </div>

                    @if($existingReview?->comment)
                        <p class="mt-2 text-xs italic text-slate-600 leading-relaxed bg-white/80 p-2.5 rounded-lg border border-amber-200/60">
                            "{{ $existingReview->comment }}"
                        </p>
                    @endif
                </div>
            </div>
        @elseif($isCustomer)
            <div>
                <div class="flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-600 text-[10px] font-bold text-white">&check;</span>
                    <h3 class="font-bold text-slate-900 text-sm">Pekerjaan Telah Selesai!</h3>
                </div>
                <p class="mt-1 text-xs text-slate-600">
                    Terima kasih telah menggunakan Bantuin. Silakan berikan penilaian dan ulasan untuk penyedia jasa.
                </p>

                <form action="{{ route('chat.review', $conversation) }}" method="POST" class="mt-3 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Rating Kepuasan</label>
                        <div class="flex items-center gap-3">
                            @for($i = 1; $i <= 5; $i++)
                                <label class="inline-flex items-center gap-1 cursor-pointer rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-medium text-slate-700 hover:border-amber-400 hover:bg-amber-50/50">
                                    <input type="radio" name="rating" value="{{ $i }}" {{ old('rating', 5) == $i ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-400">
                                    <span>{{ $i }} &#9733;</span>
                                </label>
                            @endfor
                        </div>
                        @error('rating')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="review-comment" class="block text-xs font-semibold text-slate-700 mb-1">Komentar / Ulasan (Opsional)</label>
                        <textarea 
                            name="comment" 
                            id="review-comment" 
                            rows="2" 
                            placeholder="Ceritakan pengalaman kepuasan hasil kerja dan komunikasi..." 
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-800 placeholder-slate-400 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"
                        >{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-amber-600 transition">
                        Kirim Ulasan
                    </button>
                </form>
            </div>
        @else
            <div class="flex items-center justify-center gap-2 py-1 text-xs text-slate-600">
                <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-emerald-600 text-[10px] text-white">&check;</span>
                <span class="font-semibold text-emerald-800">Pekerjaan telah ditandai selesai oleh pelanggan.</span>
                <span>Menunggu ulasan.</span>
            </div>
        @endif
    </div>
@endif
