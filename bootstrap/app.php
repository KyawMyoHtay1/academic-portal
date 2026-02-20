<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('attendance:send-low-attendance-alerts')->daily();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Register role-based access control middleware
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
            'nocache' => \App\Http\Middleware\NoCache::class,
        ]);

        // Exclude Stripe webhook from CSRF protection
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (PostTooLargeException $exception, Request $request) {
            $limit = (string) (ini_get('post_max_size') ?: 'server limit');
            $message = "Upload failed: submitted form data exceeds server limit ({$limit}). Please use a smaller file and try again.";

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'errors' => [
                        'file' => [$message],
                    ],
                ], 413);
            }

            $target = (string) ($request->headers->get('referer') ?: url('/'));

            return redirect()->to($target, 303)->with('error', $message);
        });
    })->create();
