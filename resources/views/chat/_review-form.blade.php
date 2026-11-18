@php
    $user = Auth::user();
    $isCustomer = $conversation->customer_id === $user->id;
    $hasReviewed = $conversation->hasReviewedBy($user);
    $existingReview = $conversation->reviews->first();
@endphp

@if($conversation->isCompleted())
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-4 shadow-sm">
        @if($hasReviewed)
            <div class="flex items-start gap-3">
                <div class="text-amber-500 text-2xl mt-0.5">&starf;</div>
                <div>
                    <h3 class="font-bold text-gray-800">Ulasan Telah Diberikan</h3>
                    <div class="flex items-center gap-1 my-1 text-amber-500">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= ($existingReview?->rating ?? 0))
                                <span>&#9733;</span>
                            @else
                                <span class="text-gray-300">&#9733;</span>
                            @endif
                        @endfor
                        <span class="text-xs font-semibold text-gray-600 ml-1">({{ $existingReview?->rating ?? 0 }}/5)</span>
                    </div>
                    @if($existingReview?->comment)
                        <p class="text-sm text-gray-600 mt-1 italic">"{{ $existingReview->comment }}"</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-2">Diberikan pada {{ $existingReview?->created_at?->format('d M Y, H:i') }}</p>
                </div>
            </div>
        @elseif($isCustomer)
            <div>
                <h3 class="font-bold text-gray-900 text-base mb-1">Pekerjaan Telah Selesai!</h3>
                <p class="text-xs text-gray-600 mb-4">Bagikan pengalaman Anda dan berikan ulasan untuk penyedia jasa ini.</p>

                <form action="{{ route('chat.review', $conversation) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Rating Kepuasan (1 - 5 Bintang)</label>
                        <div class="flex items-center gap-4">
                            @for($i = 1; $i <= 5; $i++)
                                <label class="inline-flex items-center gap-1 cursor-pointer text-sm">
                                    <input type="radio" name="rating" value="{{ $i }}" {{ old('rating', 5) == $i ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-400">
                                    <span>{{ $i }} &#9733;</span>
                                </label>
                            @endfor
                        </div>
                        @error('rating')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="review-comment" class="block text-xs font-semibold text-gray-700 mb-1">Komentar / Ulasan (Opsional)</label>
                        <textarea 
                            name="comment" 
                            id="review-comment" 
                            rows="2" 
                            placeholder="Ceritakan kepuasan hasil kerja, kecepatan respon, dan kualitas layanan..." 
                            class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:outline-none"
                        >{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs rounded-lg transition shadow-sm">
                        Kirim Ulasan
                    </button>
                </form>
            </div>
        @else
            <div class="text-center py-2 text-sm text-gray-600">
                <span class="font-semibold text-green-700">&check; Percakapan ini telah selesai.</span> Menunggu ulasan dari pelanggan.
            </div>
        @endif
    </div>
@endif

