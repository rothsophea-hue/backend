<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
    $path = '/tmp/laravel-storage';
    foreach ([
        'framework/views',
        'framework/sessions',
        'framework/cache/data',
        'logs',
    ] as $dir) {
        @mkdir($path.'/'.$dir, 0775, true);
    }
    @mkdir('/tmp/views', 0775, true);
    $app->useStoragePath($path);
}

return $app;
