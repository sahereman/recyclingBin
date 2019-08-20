<?php

namespace App\Providers;

use App\Models\Config;
use App\Models\User;
use App\Observers\ConfigObserver;
use App\Observers\UserObserver;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Config::observe(ConfigObserver::class);
        User::observe(UserObserver::class);

        // Carbon 中文化配置
        Carbon::setLocale('zh');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
