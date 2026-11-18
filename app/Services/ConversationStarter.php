<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\ServiceOffer;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ConversationStarter
{
    /**
     * Start or retrieve an existing conversation based on a listing.
     *
     * Rules:
     * - Admin cannot initiate chat.
     * - Provider initiates chat from an open ServiceRequest (created by a customer).
     * - Customer initiates chat from an active ServiceOffer (created by a provider).
     * - Prevents duplicate conversations for the same user pair and listing.
     *
     * @param User $user
     * @param array $data ['service_request_id' => ..., 'service_offer_id' => ...]
     * @return Conversation
     * @throws ValidationException
     */
    public function start(User $user, array $data): Conversation
    {
        if ($user->role === 'admin') {
            throw ValidationException::withMessages([
                'user' => ['Admin tidak diizinkan untuk memulai percakapan.'],
            ]);
        }

        $serviceRequestId = $data['service_request_id'] ?? null;
        $serviceOfferId = $data['service_offer_id'] ?? null;

        if (!$serviceRequestId && !$serviceOfferId) {
            throw ValidationException::withMessages([
                'listing' => ['Percakapan harus dimulai dari Butuh Jasa atau Menyediakan Jasa.'],
            ]);
        }

        if ($serviceRequestId && $serviceOfferId) {
            throw ValidationException::withMessages([
                'listing' => ['Percakapan tidak boleh terhubung ke dua jenis listing sekaligus.'],
            ]);
        }

        if ($serviceRequestId) {
            return $this->startFromRequest($user, (int) $serviceRequestId);
        }

        return $this->startFromOffer($user, (int) $serviceOfferId);
    }

    protected function startFromRequest(User $user, int $requestId): Conversation
    {
        if ($user->role !== 'provider') {
            throw ValidationException::withMessages([
                'role' => ['Hanya penyedia jasa yang dapat memulai percakapan dari Butuh Jasa.'],
            ]);
        }

        $request = ServiceRequest::findOrFail($requestId);

        if ($request->status !== 'open') {
            throw ValidationException::withMessages([
                'service_request' => ['Permintaan jasa ini sudah tidak terbuka.'],
            ]);
        }

        if ($request->customer_id === $user->id) {
            throw ValidationException::withMessages([
                'user' => ['Anda tidak dapat memulai percakapan dengan permintaan jasa milik sendiri.'],
            ]);
        }

        return Conversation::firstOrCreate([
            'customer_id' => $request->customer_id,
            'provider_id' => $user->id,
            'service_request_id' => $request->id,
        ], [
            'status' => 'active',
        ]);
    }

    protected function startFromOffer(User $user, int $offerId): Conversation
    {
        if ($user->role !== 'customer') {
            throw ValidationException::withMessages([
                'role' => ['Hanya pelanggan yang dapat memulai percakapan dari Menyediakan Jasa.'],
            ]);
        }

        $offer = ServiceOffer::findOrFail($offerId);

        if ($offer->status !== 'active') {
            throw ValidationException::withMessages([
                'service_offer' => ['Penawaran jasa ini sedang tidak aktif.'],
            ]);
        }

        if ($offer->provider_id === $user->id) {
            throw ValidationException::withMessages([
                'user' => ['Anda tidak dapat memulai percakapan dengan penawaran jasa milik sendiri.'],
            ]);
        }

        return Conversation::firstOrCreate([
            'customer_id' => $user->id,
            'provider_id' => $offer->provider_id,
            'service_offer_id' => $offer->id,
        ], [
            'status' => 'active',
        ]);
    }
}

