<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Admin area lives in its own file (routes/admin.php) for
            // manageability. It needs the `web` middleware group (sessions,
            // CSRF, TrackPageVisits) explicitly — the default web file gets
            // it automatically, extra files must opt in.
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust Vercel's edge proxy so Laravel sees the original https scheme
        // (X-Forwarded-Proto) and generates https:// asset/route URLs instead
        // of http:// ones (otherwise browsers block assets as mixed content).
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
        ]);

        // Record every public page view into page_visits (traffic analytics).
        $middleware->web(append: [
            \App\Http\Middleware\TrackPageVisits::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
