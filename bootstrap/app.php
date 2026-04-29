<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 1. Make it GLOBAL for all web routes
        $middleware->web(append: [
            \App\Http\Middleware\PreventBackHistory::class,
        ]);

        // 2. Keep your role alias for specific routes
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'prevent-back' => \App\Http\Middleware\PreventBackHistory::class, // Add this line!
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();