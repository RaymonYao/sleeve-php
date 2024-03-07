<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'PRC'));

$app = new Laravel\Lumen\Application(dirname(__DIR__));
$app->withFacades();
$app->withEloquent();
$app->singleton(Illuminate\Contracts\Debug\ExceptionHandler::class, App\Exceptions\Handler::class);
$app->singleton(Illuminate\Contracts\Console\Kernel::class, App\Console\Kernel::class);
foreach (['app', 'dingtalk', 'ticket', 'oss'] as $configKey) {
    $app->configure($configKey);
}
$app->middleware([App\Http\Middleware\Cors::class]);
if (env('APP_ENV') != 'production') {
    $app->middleware([App\Http\Middleware\ReqLog::class]);
}
$app->routeMiddleware(['jwt' => App\Http\Middleware\Jwt::class]);

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/api.php';
});

return $app;
