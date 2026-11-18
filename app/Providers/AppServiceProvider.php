<?php

namespace App\Providers;

use App\Models\Conversation;
use App\Models\ServiceOffer;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Policies\ConversationPolicy;
use App\Policies\ServiceOfferPolicy;
use App\Policies\ServiceRequestPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Pendaftaran Policy Otorisasi Kepemilikan Data
        Gate::policy(ServiceRequest::class, ServiceRequestPolicy::class);
        Gate::policy(ServiceOffer::class, ServiceOfferPolicy::class);
        Gate::policy(Conversation::class, ConversationPolicy::class);

        // Otorisasi Global Admin Gate
        Gate::define('admin', function (User $user) {
            return $user->role === 'admin';
        });

        // Otorisasi role-specific gates
        Gate::define('customer', function (User $user) {
            return $user->role === 'customer';
        });

        Gate::define('provider', function (User $user) {
            return $user->role === 'provider';
        });
    }
}
