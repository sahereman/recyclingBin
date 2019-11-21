<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     * @var array
     */
    protected $policies = [
        // 客户端
        'App\Models\ClientOrder' => 'App\Policies\Client\ClientOrderPolicy',
        'App\Models\BinToken' => 'App\Policies\Client\BinTokenPolicy',
        'App\Models\BoxOrder' => 'App\Policies\Client\BoxOrderPolicy',

        // 回收端
        'App\Models\CleanOrder' => 'App\Policies\Clean\CleanOrderPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
