<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    /**
     * Store a customer review for a completed conversation/transaction.
     *
     * Rules:
     * - Only customer can leave a review.
     * - Conversation must be completed (completed_at != null).
     * - Must be a participant of the conversation.
     * - Only 1 review per conversation for the reviewer.
     * - Rating integer 1-5, comment optional string.
     */
    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $user = Auth::user();

        // 1. Participant check
        if (!$conversation->hasParticipant($user)) {
            abort(403, 'Anda bukan peserta dalam percakapan ini.');
        }

        // 2. Only customer can submit review
        if ($conversation->customer_id !== $user->id) {
            abort(403, 'Hanya pelanggan yang dapat memberikan ulasan atas jasa.');
        }

        // 3. Conversation must be completed
        if (!$conversation->isCompleted()) {
            return back()->with('error', 'Ulasan hanya dapat diberikan setelah pekerjaan ditandai selesai.');
        }

        // 4. Check if already reviewed (unique per conversation & reviewer)
        if ($conversation->hasReviewedBy($user)) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk percakapan ini.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ], [
            'rating.required' => 'Pilih nilai rating bintang 1 hingga 5.',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
            'comment.max' => 'Komentar tidak boleh lebih dari 1000 karakter.',
        ]);

        Review::create([
            'conversation_id' => $conversation->id,
            'reviewer_id' => $user->id,
            'reviewee_id' => $conversation->provider_id,
            'rating' => (int) $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return redirect()->route('chat.show', $conversation)
            ->with('success', 'Terima kasih! Ulasan Anda berhasil disimpan.');
    }
}

