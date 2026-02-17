<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NoCache
{
    /**
     * Add headers to prevent browser from caching protected pages,
     * so hitting Back after logout won’t show stale dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // StreamedResponse uses headers->set() instead of header()
        if ($response instanceof StreamedResponse) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        } else {
            $response->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }

        return $response;
    }
}
