<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * @param  array<int, string>  $roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (! $user) {
            abort(401);
        }

        if ($roles === []) {
            return $next($request);
        }

        if (! in_array((string) $user->role, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
