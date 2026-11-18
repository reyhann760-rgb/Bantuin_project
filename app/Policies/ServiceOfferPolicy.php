<?php

namespace App\Policies;

use App\Models\ServiceOffer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServiceOfferPolicy
{
    /**
     * Siapa saja (publik/guest) boleh melihat daftar dan detail penawaran.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, ServiceOffer $serviceOffer): bool
    {
        return true;
    }

    /**
     * Hanya pengguna dengan role 'provider' yang boleh membuat penawaran.
     */
    public function create(User $user): bool
    {
        return $user->role === 'provider';
    }

    /**
     * Hanya pemilik penawaran yang boleh mengedit atau memperbarui.
     */
    public function update(User $user, ServiceOffer $serviceOffer): bool
    {
        return $user->id === $serviceOffer->provider_id;
    }

    /**
     * Pemilik atau Admin boleh menghapus penawaran.
     */
    public function delete(User $user, ServiceOffer $serviceOffer): bool
    {
        return $user->id === $serviceOffer->provider_id || $user->role === 'admin';
    }
}

