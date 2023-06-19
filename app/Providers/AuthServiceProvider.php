<?php

namespace App\Providers;

use App\Models\Entry;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('company', function (User $user) {
            return isset($user->company);
        });

        Gate::define('user', function (User $user) {
            return !(isset($user->company));
        });

        Gate::define('message', function (User $user, Entry $entry) {
            return $user->id === $entry->user_id
                || $user->id === $entry->jobOffer->company->user_id;
        });
    }
}