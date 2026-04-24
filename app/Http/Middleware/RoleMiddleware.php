<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Restrict the route to users with one of the given roles.
     * Usage: middleware('role:admin') or middleware('role:admin,teacher')
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->guest(route('login'));
        }

        $allowed = [];
        foreach ($roles as $segment) {
            foreach (array_map('trim', explode(',', (string) $segment)) as $r) {
                if ($r !== '') {
                    $allowed[] = $r;
                }
            }
        }

        if ($allowed === [] || !in_array(Auth::user()->role, $allowed, true)) {
            abort(403);
        }

        return $next($request);
    }
}
