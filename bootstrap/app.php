<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\HandleInertiaRequests;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            \App\Http\Middleware\CaptureUserTimezone::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
        // Set directly by JS (resources/js/bootstrap.js), not by a Laravel
        // response, so it isn't in Laravel's encrypted cookie format -
        // without this it would silently decrypt to null on every request.
        $middleware->encryptCookies(except: ['timezone']);
        $middleware->alias([
            'project.role' => \App\Http\Middleware\EnsureProjectRole::class,
        ]);
        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
