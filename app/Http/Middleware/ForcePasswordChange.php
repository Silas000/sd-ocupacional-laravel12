<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Rotas liberadas enquanto a troca obrigatória de senha está pendente.
     *
     * @var array<int, string>
     */
    private const ALLOWED_ROUTES = [
        'force-password-change',
        'force-password-change.update',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->must_change_password) {
            return $next($request);
        }

        if ($request->routeIs(...self::ALLOWED_ROUTES)) {
            return $next($request);
        }

        return redirect()->route('force-password-change');
    }
}
