<?php

namespace Tourad\UserManager\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  ...$types
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$types)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!method_exists($user, 'hasAnyUserType')) {
            return $next($request);
        }

        if (!$user->hasAnyUserType($types)) {
            abort(403, 'Unauthorized. Required user type: ' . implode(' or ', $types));
        }

        return $next($request);
    }
}