<?php

namespace Tourad\UserManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use App\Models\UserSession;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $sessions = UserSession::where('user_id', $user->id)
            ->where('is_active', true)
            ->latest()
            ->get();

        return view('user-manager::profile.index', compact('user', 'sessions'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
        ]);

        $user->update($validated);

        return redirect()->route('user-manager.profile')
            ->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('user-manager.profile')
            ->with('success', 'تم تحديث كلمة المرور بنجاح');
    }

    public function terminateSession(Request $request)
    {
        $sessionId = $request->get('session_id');

        UserSession::where('id', $sessionId)
            ->where('user_id', auth()->id())
            ->update([
                'is_active' => false,
                'ended_at' => now(),
            ]);

        return response()->json(['success' => 'تم إنهاء الجلسة بنجاح']);
    }
}