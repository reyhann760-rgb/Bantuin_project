<?php

namespace App\Policies;

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServiceRequestPolicy
{
    /**
     * Siapa saja (publik/guest) boleh melihat daftar dan detail permintaan.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, ServiceRequest $serviceRequest): bool
    {
        return true;
    }

    /**
     * Hanya pengguna dengan role 'customer' yang boleh membuat permintaan jasa.
     */
    public function create(User $user): bool
    {
        return $user->role === 'customer';
    }

    /**
     * Hanya pemilik permintaan yang boleh mengedit atau memperbarui.
     */
    public function update(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->id === $serviceRequest->customer_id;
    }

    /**
     * Pemilik atau Admin boleh menghapus permintaan.
     */
    public function delete(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->id === $serviceRequest->customer_id || $user->role === 'admin';
    }
}

