<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    /**
     * -------------------------------------------------------------------------
     * Middleware configuration (Laravel 11 way)
     * -------------------------------------------------------------------------
     *
     * This replaces the old app/Http/Kernel.php file.
     * Here we register global middleware, groups, and aliases.
     */
    ->withMiddleware(function (Middleware $middleware): void {

        /**
         * Route middleware aliases.
         *
         * This allows us to use:
         *   ->middleware('role:doctor')
         *   ->middleware('role:doctor,nurse')
         *
         * without touching controllers.
         */
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
