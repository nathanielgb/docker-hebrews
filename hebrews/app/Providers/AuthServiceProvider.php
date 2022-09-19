<?php

namespace App\Providers;

use App\Models\AccessAction;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Checks if user is allowed to access the records
        // in the rights table
        Gate::define('access', function ($user, $action) {
            if (! $user->_role) {
                $user->_role = AccessAction::where('name', '=', $user->type)->first();
            }

            $role = $user->_role;
            $access_actions = $role->access;

            return in_array($action, $access_actions);
        });

    }
}
