<?php

namespace Tourad\UserManager\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tourad\UserManager\Models\UserLoginAttempt;

class CheckLoginAttempts
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!config('user-manager.login_attempts.enabled')) {
            return $next($request);
        }

        $email = $request->input('email');
        $ip = $request->ip();

        if (!$email) {
            return $next($request);
        }

        $maxAttempts = config('user-manager.login_attempts.max_attempts', 5);
        $lockoutTime = config('user-manager.login_attempts.lockout_time', 15);

        // Check if email is locked out
        if (UserLoginAttempt::isEmailLockedOut($email, $maxAttempts, $lockoutTime)) {
            return response()->json([
                'message' => 'Too many login attempts. Please try again later.',
                'email_locked' => true,
            ], 429);
        }

        // Check if IP is locked out
        if (UserLoginAttempt::isIpLockedOut($ip, $maxAttempts, $lockoutTime)) {
            return response()->json([
                'message' => 'Too many login attempts from this IP. Please try again later.',
                'ip_locked' => true,
            ], 429);
        }

        return $next($request);
    }
}