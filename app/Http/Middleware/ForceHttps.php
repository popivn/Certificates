<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only force HTTPS in production
        if (App::environment('production') && config('app.force_https', false)) {
            // Check if the request is not already HTTPS
            if (!$request->secure() && !$request->is('health') && !$request->is('ping')) {
                // Redirect to HTTPS version
                $url = str_replace('http://', 'https://', $request->fullUrl());
                return redirect($url, 301);
            }
        }

        return $next($request);
    }
}
