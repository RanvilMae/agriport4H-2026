<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsManager;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 1. Register Route Middleware Aliases
        $middleware->alias([
            'is_manager' => EnsureUserIsManager::class,
            'is_admin'   => EnsureUserIsAdmin::class,
        ]);

        // 2. Append Custom Global Headers or Security Middleware
        $middleware->web(append: [
            // Add custom web middleware here if needed (e.g., SecurityHeaders::class)
        ]);

        // 3. Configure Trust Proxies if running behind HTTPS Load Balancers / Reverse Proxies
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Custom Exception Handling: Format Authorization Failures Gracefully
        $exceptions->render(function (AccessDeniedHttpException|AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'Unauthorized action.',
                ], 403);
            }

            return back()->with('error', $e->getMessage() ?: 'You do not have permission to perform this action.');
        });
    })->create();