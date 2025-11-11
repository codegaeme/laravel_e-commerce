<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // --- 3. Middleware Aliases ---
        $middleware->alias([

            'admin' => \App\Http\Middleware\CheckRole::class,
            // Thêm alias của riêng bạn:
            'staff' => \App\Http\Middleware\CheckRoleStaff::class,
            'ship' => \App\Http\Middleware\CheckRoleShip::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
