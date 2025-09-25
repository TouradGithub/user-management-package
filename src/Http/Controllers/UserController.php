<?php

namespace Tourad\UserManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\User;
use Tourad\UserManager\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request): View
    {
        $query = User::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }

        $users = $query->with('userType')->paginate(15);

        return view('user-manager::users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create(): View
    {
        return view('user-manager::users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'user_type_id' => 'nullable|exists:user_types,id',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        return redirect()->route('user-manager.users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * Display the specified user
     */
    public function show(User $user): View
    {
        return view('user-manager::users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user): View
    {
        return view('user-manager::users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'user_type_id' => 'nullable|exists:user_types,id',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('user-manager.users.index')
            ->with('success', 'تم تحديث المستخدم بنجاح');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('user-manager.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    /**
     * Handle bulk actions
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $action = $request->get('action');
        $userIds = $request->get('user_ids', []);

        if (empty($userIds)) {
            return response()->json(['error' => 'لم يتم تحديد أي مستخدم'], 400);
        }

        switch ($action) {
            case 'activate':
                User::whereIn('id', $userIds)->update(['is_active' => true]);
                return response()->json(['success' => 'تم تفعيل المستخدمين بنجاح']);

            case 'deactivate':
                User::whereIn('id', $userIds)->update(['is_active' => false]);
                return response()->json(['success' => 'تم إلغاء تفعيل المستخدمين بنجاح']);

            case 'delete':
                User::whereIn('id', $userIds)->delete();
                return response()->json(['success' => 'تم حذف المستخدمين بنجاح']);

            default:
                return response()->json(['error' => 'إجراء غير صالح'], 400);
        }
    }

    /**
     * Export users
     */
    public function export(Request $request)
    {
        // Implementation for user export
        return response()->json(['message' => 'تصدير المستخدمين - قيد التطوير']);
    }

    /**
     * Import users
     */
    public function import(Request $request)
    {
        // Implementation for user import
        return response()->json(['message' => 'استيراد المستخدمين - قيد التطوير']);
    }
}