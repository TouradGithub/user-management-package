<?php

namespace Tourad\UserManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Tourad\UserManager\Models\UserType;

class UserTypeController extends Controller
{
    public function index(): View
    {
        $userTypes = UserType::withCount('users')->paginate(15);
        return view('user-manager::user-types.index', compact('userTypes'));
    }

    public function create(): View
    {
        return view('user-manager::user-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:user_types',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        UserType::create($validated);

        return redirect()->route('user-manager.user-types.index')
            ->with('success', 'تم إنشاء نوع المستخدم بنجاح');
    }

    public function edit(UserType $userType): View
    {
        return view('user-manager::user-types.edit', compact('userType'));
    }

    public function update(Request $request, UserType $userType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:user_types,name,' . $userType->id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $userType->update($validated);

        return redirect()->route('user-manager.user-types.index')
            ->with('success', 'تم تحديث نوع المستخدم بنجاح');
    }

    public function destroy(UserType $userType)
    {
        if ($userType->users()->count() > 0) {
            return redirect()->route('user-manager.user-types.index')
                ->with('error', 'لا يمكن حذف نوع المستخدم لأن هناك مستخدمين مرتبطين به');
        }

        $userType->delete();

        return redirect()->route('user-manager.user-types.index')
            ->with('success', 'تم حذف نوع المستخدم بنجاح');
    }

    public function toggle(UserType $userType)
    {
        $userType->update(['is_active' => !$userType->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $userType->is_active,
            'message' => $userType->is_active ? 'تم تفعيل نوع المستخدم' : 'تم إلغاء تفعيل نوع المستخدم'
        ]);
    }
}