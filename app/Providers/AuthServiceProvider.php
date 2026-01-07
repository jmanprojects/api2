<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Policies\AppointmentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * This tells Laravel which Policy class controls authorization
     * for each Model.
     */
    protected $policies = [
        Appointment::class => AppointmentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        /**
         * Register policies.
         *
         * IMPORTANT:
         * - In older Laravel versions, this was required explicitly.
         * - In modern Laravel, having the $policies map is enough,
         *   but calling registerPolicies() keeps it explicit and clear.
         */
        $this->registerPolicies();
    }
}
