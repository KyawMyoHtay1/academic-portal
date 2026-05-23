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

        // Allow same-origin embedding only for the manual PDF preview route.
        $allowSameOriginEmbedding = $request->routeIs('guest.user-manual.view');
        $response->headers->set('X-Frame-Options', $allowSameOriginEmbedding ? 'SAMEORIGIN' : 'DENY');
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
            'https://translate-pa.googleapis.com',
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
            'https://translate-pa.googleapis.com',
            'https://translate.googleapis.com',
            'https://translate.google.com',
        ];
        $workerSources = ["'self'"];

        if ($isLocal) {
            // Allow Vite/HMR regardless of localhost/loopback port in local development.
            $scriptSources[] = 'http:';
            $styleSources[] = 'http:';
            $connectSources[] = 'http:';
            $connectSources[] = 'ws:';
            $workerSources[] = 'blob:';
            $workerSources[] = 'http:';
        }

        $frameAncestors = $allowSameOriginEmbedding ? "'self'" : "'none'";

        $csp = implode('; ', [
            "default-src 'self'",
            'script-src '.implode(' ', $scriptSources),
            'script-src-elem '.implode(' ', $scriptSources),
            'style-src '.implode(' ', $styleSources),
            'style-src-elem '.implode(' ', $styleSources),
            "img-src 'self' data: blob: https:",
            "media-src 'self' data: blob: https:",
            "font-src 'self' data: https://fonts.gstatic.com https://fonts.bunny.net",
            'connect-src '.implode(' ', $connectSources),
            'worker-src '.implode(' ', $workerSources),
            "frame-src 'self' https://js.stripe.com https://hooks.stripe.com https://www.google.com https://www.gstatic.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors {$frameAncestors}",
            'upgrade-insecure-requests',
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        if (app()->isProduction() && $request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
