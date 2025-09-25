<?php

namespace Tourad\UserManager\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tourad\UserManager\Models\UserSession;

class TrackUserSession
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
        if (!config('user-manager.session.track_sessions') || !auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        $sessionId = session()->getId();
        $userAgent = $request->userAgent();
        $ipAddress = $request->ip();

        // Find or create session record
        $userSession = UserSession::updateOrCreate(
            [
                'user_id' => $user->id,
                'session_id' => $sessionId,
            ],
            [
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'device_info' => $this->parseDeviceInfo($userAgent),
                'last_activity' => now(),
                'is_active' => true,
            ]
        );

        // Check concurrent sessions limit
        $maxSessions = config('user-manager.session.max_concurrent_sessions');
        if ($maxSessions && $maxSessions > 0) {
            $activeSessions = UserSession::where('user_id', $user->id)
                ->where('is_active', true)
                ->count();

            if ($activeSessions > $maxSessions) {
                // Terminate oldest sessions
                UserSession::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->where('session_id', '!=', $sessionId)
                    ->orderBy('last_activity')
                    ->take($activeSessions - $maxSessions)
                    ->update(['is_active' => false]);
            }
        }

        return $next($request);
    }

    /**
     * Parse device information from user agent
     */
    private function parseDeviceInfo(string $userAgent): array
    {
        $info = [
            'device_type' => 'desktop',
            'browser' => 'unknown',
            'os' => 'unknown',
        ];

        // Detect device type
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            if (preg_match('/iPad/', $userAgent)) {
                $info['device_type'] = 'tablet';
            } else {
                $info['device_type'] = 'mobile';
            }
        }

        // Detect browser
        if (preg_match('/Chrome/', $userAgent)) {
            $info['browser'] = 'Chrome';
        } elseif (preg_match('/Firefox/', $userAgent)) {
            $info['browser'] = 'Firefox';
        } elseif (preg_match('/Safari/', $userAgent) && !preg_match('/Chrome/', $userAgent)) {
            $info['browser'] = 'Safari';
        } elseif (preg_match('/Edge/', $userAgent)) {
            $info['browser'] = 'Edge';
        }

        // Detect OS
        if (preg_match('/Windows/', $userAgent)) {
            $info['os'] = 'Windows';
        } elseif (preg_match('/Mac OS X/', $userAgent)) {
            $info['os'] = 'macOS';
        } elseif (preg_match('/Linux/', $userAgent)) {
            $info['os'] = 'Linux';
        } elseif (preg_match('/Android/', $userAgent)) {
            $info['os'] = 'Android';
        } elseif (preg_match('/iOS/', $userAgent)) {
            $info['os'] = 'iOS';
        }

        return $info;
    }
}