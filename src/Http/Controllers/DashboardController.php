<?php

namespace Tourad\UserManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserType;
use App\Models\UserActivity;
use App\Models\UserSession;
use App\Models\UserLoginAttempt;
use Tourad\UserManager\UserManagerService;

class DashboardController extends Controller
{
    protected $userManager;

    public function __construct(UserManagerService $userManager)
    {
        $this->middleware('auth');
        $this->userManager = $userManager;
    }

    /**
     * Show the dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user statistics
        $stats = $this->getUserStats();
        
        // Get recent activities
        $recentActivities = UserActivity::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get user's active sessions
        $activeSessions = $user->sessions()->where('is_active', true)->get();
        
        // Get recent login attempts
        $recentLoginAttempts = UserLoginAttempt::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('user-manager::dashboard.index', compact(
            'user',
            'stats',
            'recentActivities',
            'activeSessions',
            'recentLoginAttempts'
        ));
    }

    /**
     * Get user statistics
     */
    protected function getUserStats(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        $unverifiedUsers = User::whereNull('email_verified_at')->count();
        
        // Users created today
        $usersToday = User::whereDate('created_at', today())->count();
        
        // Users created this week
        $usersThisWeek = User::whereBetween('created_at', [
            now()->startOfWeek(), 
            now()->endOfWeek()
        ])->count();
        
        // Users created this month
        $usersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Recently active users (last 7 days)
        $recentlyActive = User::where('updated_at', '>=', now()->subDays(7))->count();
        
        // User types distribution
        $userTypes = UserType::withCount('users')
            ->get()
            ->pluck('users_count', 'name')
            ->toArray();
        
        // Active sessions count
        $activeSessions = UserSession::where('is_active', true)->count();
        
        // Failed login attempts today
        $failedAttemptsToday = UserLoginAttempt::where('is_successful', false)
            ->whereDate('created_at', today())
            ->count();
        
        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'inactive_users' => $inactiveUsers,
            'verified_users' => $verifiedUsers,
            'unverified_users' => $unverifiedUsers,
            'deleted_users' => $deletedUsers,
            'users_today' => $usersToday,
            'users_this_week' => $usersThisWeek,
            'users_this_month' => $usersThisMonth,
            'recently_active' => $recentlyActive,
            'user_types' => $userTypes,
            'active_sessions' => $activeSessions,
            'failed_attempts_today' => $failedAttemptsToday,
        ];
    }

    /**
     * Get users list
     */
    public function users(Request $request)
    {
        $filters = $request->only([
            'search', 'user_type', 'status', 'role', 
            'date_from', 'date_to', 'sort_by', 'sort_order', 'per_page'
        ]);

        $users = $this->userManager->getUsers($filters);
        $userTypes = UserType::active()->get();

        return view('user-manager::dashboard.users', compact('users', 'userTypes', 'filters'));
    }

    /**
     * Get user types list
     */
    public function userTypes()
    {
        $userTypes = UserType::withCount('users')->ordered()->get();
        
        return view('user-manager::dashboard.user-types', compact('userTypes'));
    }

    /**
     * Get activities log
     */
    public function activities(Request $request)
    {
        $query = UserActivity::with('user')->orderBy('created_at', 'desc');
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $activities = $query->paginate(20);
        $users = User::select('id', 'name')->get();
        
        return view('user-manager::dashboard.activities', compact('activities', 'users'));
    }

    /**
     * Get sessions list
     */
    public function sessions()
    {
        $sessions = UserSession::with('user')
            ->orderBy('last_activity', 'desc')
            ->paginate(20);
            
        return view('user-manager::dashboard.sessions', compact('sessions'));
    }

    /**
     * Terminate user session
     */
    public function terminateSession(Request $request, $sessionId)
    {
        $session = UserSession::findOrFail($sessionId);
        $session->terminate();
        
        UserActivity::log('session_terminated', 'Session terminated by admin', $session->user);
        
        return back()->with('success', 'Session terminated successfully.');
    }

    /**
     * Get login attempts
     */
    public function loginAttempts(Request $request)
    {
        $query = UserLoginAttempt::orderBy('created_at', 'desc');
        
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        
        if ($request->filled('successful')) {
            $query->where('successful', $request->successful);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $loginAttempts = $query->paginate(20);
        
        return view('user-manager::dashboard.login-attempts', compact('loginAttempts'));
    }

    /**
     * Get profile page
     */
    public function profile()
    {
        $user = Auth::user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        $activeSessions = $user->sessions()->active()->get();
        
        return view('user-manager::dashboard.profile', compact('user', 'recentActivities', 'activeSessions'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'timezone' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:10',
        ]);
        
        $user->update($request->only([
            'name', 'email', 'username', 'phone', 'timezone', 'language'
        ]));
        
        return back()->with('success', 'Profile updated successfully.');
    }
}