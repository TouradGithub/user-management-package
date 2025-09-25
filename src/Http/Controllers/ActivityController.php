<?php

namespace Tourad\UserManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Tourad\UserManager\Models\UserActivity;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $query = UserActivity::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->get('activity_type'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $activities = $query->latest()->paginate(20);

        return view('user-manager::activities.index', compact('activities'));
    }

    public function show(UserActivity $activity): View
    {
        return view('user-manager::activities.show', compact('activity'));
    }

    public function destroy(UserActivity $activity)
    {
        $activity->delete();

        return redirect()->route('user-manager.activities.index')
            ->with('success', 'تم حذف النشاط بنجاح');
    }

    public function bulkDelete(Request $request)
    {
        $activityIds = $request->get('activity_ids', []);

        if (empty($activityIds)) {
            return response()->json(['error' => 'لم يتم تحديد أي نشاط'], 400);
        }

        UserActivity::whereIn('id', $activityIds)->delete();

        return response()->json(['success' => 'تم حذف الأنشطة بنجاح']);
    }
}