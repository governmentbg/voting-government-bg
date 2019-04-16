<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Auth\EloquentBackendUserProvider;
use App\Auth\EloquentFrontendUserProvider;
use Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('eloquent.backend_user', function($app, array $config) {
            return new EloquentBackendUserProvider($app['hash'], $config['model']);
        });
        
        Auth::provider('eloquent.frontend_user', function($app, array $config) {
            return new EloquentFrontendUserProvider($app['hash'], $config['model']);
        });
    }
}
