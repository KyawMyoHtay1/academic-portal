<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        $isLocal = app()->environment('local');
        $scriptSources = [
            "'self'",
            "'unsafe-inline'",
            "'unsafe-eval'",
            'https://js.stripe.com',
            'https://www.google.com',
            'https://www.gstatic.com',
            'https://translate.google.com',
            'https://translate.googleapis.com',
        ];
        $styleSources = [
            "'self'",
            "'unsafe-inline'",
            'https://fonts.googleapis.com',
            'https://fonts.bunny.net',
            'https://www.gstatic.com',
        ];
        $connectSources = [
            "'self'",
            'https://api.stripe.com',
            'https://www.google.com',
            'https://www.gstatic.com',
            'https://translate.googleapis.com',
            'https://translate.google.com',
        ];

        if ($isLocal) {
            // Allow Vite/HMR regardless of localhost/loopback port in local development.
            $scriptSources[] = 'http:';
            $connectSources[] = 'http:';
            $connectSources[] = 'ws:';
        }

        $csp = implode('; ', [
            "default-src 'self'",
            'script-src '.implode(' ', $scriptSources),
            'script-src-elem '.implode(' ', $scriptSources),
            'style-src '.implode(' ', $styleSources),
            'style-src-elem '.implode(' ', $styleSources),
            "img-src 'self' data: blob: https:",
            "font-src 'self' data: https://fonts.gstatic.com https://fonts.bunny.net",
            'connect-src '.implode(' ', $connectSources),
            "frame-src 'self' https://js.stripe.com https://hooks.stripe.com https://www.google.com https://www.gstatic.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        if (app()->isProduction() && $request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
