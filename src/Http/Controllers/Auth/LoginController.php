<?php

namespace Tourad\UserManager\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tourad\UserManager\Http\Controllers\Controller;
use Tourad\UserManager\Models\User;
use Tourad\UserManager\Models\UserActivity;
use Tourad\UserManager\Models\UserLoginAttempt;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('user-manager.dashboard');
        }

        return view('user-manager::auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');
        $remember = $request->boolean('remember');
        $ip = $request->ip();

        // Check if login attempts are enabled
        if (config('user-manager.login_attempts.enabled', true)) {
            $maxAttempts = config('user-manager.login_attempts.max_attempts', 5);
            $lockoutTime = config('user-manager.login_attempts.lockout_time', 15);

            // Check if email or IP is locked out
            if (UserLoginAttempt::isEmailLockedOut($email, $maxAttempts, $lockoutTime)) {
                throw ValidationException::withMessages([
                    'email' => 'Too many login attempts. Please try again in ' . $lockoutTime . ' minutes.',
                ]);
            }

            if (UserLoginAttempt::isIpLockedOut($ip, $maxAttempts, $lockoutTime)) {
                throw ValidationException::withMessages([
                    'email' => 'Too many login attempts from this IP. Please try again later.',
                ]);
            }
        }

        // Find user
        $user = User::where('email', $email)->first();

        // Check if user exists and is active
        if (!$user) {
            UserLoginAttempt::logAttempt($email, false, 'User not found');
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        if (!$user->is_active) {
            UserLoginAttempt::logAttempt($email, false, 'Account deactivated');
            throw ValidationException::withMessages([
                'email' => 'Your account has been deactivated. Please contact support.',
            ]);
        }

        // Check password
        if (!Hash::check($password, $user->password)) {
            UserLoginAttempt::logAttempt($email, false, 'Invalid password');
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        // Successful login
        Auth::login($user, $remember);
        UserLoginAttempt::logAttempt($email, true);

        // Update last login
        $user->updateLastLogin();

        // Create session record if session tracking is enabled
        if (config('user-manager.session.track_sessions', true)) {
            $this->createSessionRecord($request, $user);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('user-manager.dashboard'))
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        if ($user && config('user-manager.session.track_sessions', true)) {
            // Mark session as inactive
            $sessionId = session()->getId();
            $userSession = $user->sessions()->where('session_id', $sessionId)->first();
            if ($userSession) {
                $userSession->terminate();
            }
        }

        // Log activity
        if ($user) {
            UserActivity::log('logout', 'User logged out', $user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user-manager.login')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Create session record
     */
    protected function createSessionRecord(Request $request, User $user): void
    {
        $sessionId = session()->getId();
        $userAgent = $request->userAgent();
        $ipAddress = $request->ip();

        // Parse device info
        $deviceInfo = $this->parseDeviceInfo($userAgent);

        // Create or update session
        $user->sessions()->updateOrCreate(
            ['session_id' => $sessionId],
            [
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'device_info' => $deviceInfo,
                'last_activity' => now(),
                'is_active' => true,
            ]
        );

        // Check concurrent sessions limit
        $maxSessions = config('user-manager.session.max_concurrent_sessions');
        if ($maxSessions && $maxSessions > 0) {
            $activeSessions = $user->sessions()->active()->count();

            if ($activeSessions > $maxSessions) {
                // Terminate oldest sessions
                $user->sessions()
                    ->active()
                    ->where('session_id', '!=', $sessionId)
                    ->orderBy('last_activity')
                    ->take($activeSessions - $maxSessions)
                    ->update(['is_active' => false]);
            }
        }
    }

    /**
     * Parse device information from user agent
     */
    protected function parseDeviceInfo(string $userAgent): array
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