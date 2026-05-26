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
    ->withMiddleware(function (Middleware $middleware): void {
        // 1. Set proxy agar HTTPS terbaca dengan benar
        $middleware->trustProxies(at: '*');
        
        // 2. Alias middleware kamu
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        
        // 3. SEBAIKNYA DIUBAH: Jangan langsung return JSON 401 untuk SEMUA rute.
        // Biarkan rute web (termasuk Filament) dialihkan ke halaman login, 
        // atau batasi JSON 401 ini hanya jika request meminta JSON (API/AJAX tertentu).
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'unauthenticated'], 401);
            }
            
            // Jika dari Filament/Web, biarkan diarahkan ke halaman login Filament
            return route('filament.admin.auth.login'); 
        });
        
        $middleware->validateCsrfTokens(except: [
            'api/payment/webhook',
        ]);
        
        $middleware->alias([
            'subscribed' => \App\Http\Middleware\EnsureSubscribed::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn ($request) => $request->is('api/*'));
    })->create();
