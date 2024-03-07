<?php

namespace App\Providers;

use App\Http\Middleware\Jwt;
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
        //默认无人登录
        $this->app->bind(Jwt::NOW_LOGIN_USER, function () {
            return null;
        });
    }
}
