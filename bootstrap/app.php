<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'guest' => RedirectIfAuthenticated::class,
            'auth' => Authenticate::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            //if guard is admin redirect to admin/dashboard
            if ($request->routeIs('admin.*')) {
                return 'admin/login';
            }
        });
        $middleware->redirectUsersTo(function (Request $request) {
            //if guard is admin redirect to admin/login
            if (Auth::guard('web')->check()) {
                if (!$request->routeIs('admin.*')) {
                    return 'admin/dashboard';
                }
            }
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
