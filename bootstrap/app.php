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
        
        // PERBAIKAN UTUH: Melindungi rute webhook dari pemblokiran token CSRF Laravel
        $middleware->validateCsrfTokens(except: [
            'webhook/xendit',
            'webhook/xendit/*', // Pola bintang agar aman jika ada sub-path
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();