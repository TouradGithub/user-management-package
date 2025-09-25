<?php

namespace Tourad\UserManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\UserSession;

class SessionController extends Controller
{
    public function index(Request $request): View
    {
        $query = UserSession::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->get('is_active') === '1');
        }

        $sessions = $query->latest()->paginate(20);

        return view('user-manager::sessions.index', compact('sessions'));
    }

    public function destroy(UserSession $session)
    {
        $session->update([
            'is_active' => false,
            'ended_at' => now(),
        ]);

        return redirect()->route('user-manager.sessions.index')
            ->with('success', 'تم إنهاء الجلسة بنجاح');
    }

    public function bulkTerminate(Request $request)
    {
        $sessionIds = $request->get('session_ids', []);

        if (empty($sessionIds)) {
            return response()->json(['error' => 'لم يتم تحديد أي جلسة'], 400);
        }

        UserSession::whereIn('id', $sessionIds)->update([
            'is_active' => false,
            'ended_at' => now(),
        ]);

        return response()->json(['success' => 'تم إنهاء الجلسات بنجاح']);
    }
}