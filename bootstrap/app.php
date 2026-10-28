<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureManager;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Honour X-Forwarded-* headers (e.g. when shared through a Cloudflare tunnel).
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [SetLocale::class]);
        $middleware->alias(['admin' => EnsureAdmin::class, 'manager' => EnsureManager::class]);
        $middleware->redirectGuestsTo(fn (Request $request) => $request->is('admin', 'admin/*', 'gestion', 'gestion/*') ? route('login') : route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
