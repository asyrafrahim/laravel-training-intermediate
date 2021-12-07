<?php

namespace App\Providers;

use App\Models\Schedule;
use App\Policies\SchedulePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Schedule::class => SchedulePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // define admin role
        Gate::define('isAdmin', function($user) {
            return $user->role == 'admin';
        });
        //define manager role
        Gate::define('isManager', function($user) {
            return $user->role == 'manager';
        });
        //define user role
        Gate::define('isUser', function($user) {
            return $user->role == 'user';
        });
    }
}
