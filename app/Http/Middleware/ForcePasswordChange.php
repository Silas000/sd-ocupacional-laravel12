<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->must_change_password) {
            if ($request->routeIs('force-password-change') || $request->routeIs('logout')) {
                return $next($request);
            }

            return redirect()->route('force-password-change');
        }

        return $next($request);
    }
}
