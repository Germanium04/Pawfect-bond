<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // CRITICAL: You must import the Model!
use App\Models\Pet;
use App\Models\Report;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard() {
        // ── Stats Cards ──
        $userCount   = User::where('role', 'pet_lover')->count();
        $petCount    = Pet::where('status', 'available')->count();
        $reportCount = Report::count();
        $rehomed     = Pet::where('status', 'rehomed')->count();

        // ── Recent Reports (last 8) ──
        $recentReports = Report::with(['reporter', 'reportedUser', 'reportedPet'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // ── Report Breakdown ──
        $reportPending   = Report::where('status', 'pending')->count();
        $reportWarned    = Report::where('status', 'warned')->count();
        $reportSuspended = Report::where('status', 'suspended')->count();
        $reportBanned    = Report::where('status', 'banned')->count();
        $reportDismissed = Report::where('status', 'dismissed')->count();

        // ── Top Active Users (most posts) ──
        $topUsers = User::where('role', 'pet_lover')
            ->withCount([
                'pets as posts_count',
                'rehomedPets as rehomed_count',
                'adoptions as adopted_count',
            ])
            ->orderBy('posts_count', 'desc')
            ->take(8)
            ->get();

        // ── Pet Status Breakdown ──
        $petAvailable = Pet::where('status', 'available')->count();
        $petRemoved   = Pet::where('status', 'removed')->count();

        // ── New Users This Month ──
        $newUsersThisMonth = User::where('role', 'pet_lover')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at',  now()->year)
            ->count();

        return view('admin.dashboard', compact(
            'userCount', 'petCount', 'reportCount', 'rehomed',
            'recentReports',
            'reportPending', 'reportWarned', 'reportSuspended', 'reportBanned', 'reportDismissed',
            'topUsers',
            'petAvailable', 'petRemoved',
            'newUsersThisMonth'
        ));
    }

    public function aboutMe_page() {
        return view('admin.about-me');
    }

    public function userManagement(Request $request)
    {
        // Start query (DO NOT call get yet)
        $query = User::withCount([
            'pets as posts_count', 
            'rehomedPets as rehomed_count',
            'adoptions as adopted_count'
        ])->where('role', 'pet_lover'); // important since you filter later anyway

        // ========================
        // SORTING
        // ========================
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'az':
                    $query->orderBy('first_name', 'asc');
                    break;
                case 'za':
                    $query->orderBy('first_name', 'desc');
                    break;
                case 'youngest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
            }
        }

        // ========================
        // SEARCH
        // ========================
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                ->orWhere('last_name', 'like', '%' . $request->search . '%');
            });
        }

        // FINAL EXECUTION
        $users = $query->get();

        // ========================
        // STATS (unchanged logic)
        // ========================
        $totalUsers = User::where('role','pet_lover')->count();

        $activePeople = User::where('status', 'active')
            ->where('role', 'pet_lover')
            ->count();

        $inactivePeople = User::where('status', '!=', 'active')
            ->where('updated_at', '<', now()->subDays(100))
            ->where('role', 'pet_lover') // 🔥 add this for consistency
            ->count();

        return view('admin.user-management', compact(
            'users',
            'totalUsers',
            'activePeople',
            'inactivePeople'
        ));
    }

    public function petListing(Request $request)
    {
        // Start query
        $query = Pet::where('status', 'available');

        // ========================
        // SORTING
        // ========================
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'newest_arrival':
                    $query->orderBy('created_at', 'desc');
                    break;

                case 'longest_stay':
                    $query->orderBy('created_at', 'asc');
                    break;

                case 'age_desc': // youngest → oldest
                    $query->orderBy('birthday', 'desc');
                    break;

                case 'age_asc': // oldest → youngest
                    $query->orderBy('birthday', 'asc');
                    break;
            }
        }

        // ========================
        // SEARCH
        // ========================
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
                // add more fields if needed:
                // ->orWhere('breed', 'like', ...)
            });
        }

        // FINAL EXECUTION
        $pets = $query->get();

        // ========================
        // STATS
        // ========================
        $totalListing = Pet::count();
        $available = Pet::where('status', 'available')->count();
        $removed = Pet::where('status', 'removed')->count();

        return view('admin.pet-listing', compact(
            'pets',
            'totalListing',
            'available',
            'removed'
        ));
    }

    public function reportsAndFlags(Request $request)
    {
        $query = Report::with(['reportedUser', 'reportedPet', 'reporter']);

        // SORTING
        if ($request->filled('sort')) {
            $query->orderBy('created_at', $request->sort === 'oldest' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // FILTER BY TYPE
        if ($request->filled('type') && $request->type !== 'All') {
            $query->where('report_type', $request->type);
        }

        // FILTER BY STATUS
        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        $report      = $query->get();
        $reportCount = Report::count();
        $pending     = Report::where('status', 'pending')->count();
        $suspended   = Report::where('status', 'suspended')->count();
        $banned      = Report::where('status', 'banned')->count();

        return view('admin.reports-flags', compact('reportCount', 'pending', 'suspended', 'banned', 'report'));
    }

    public function reportAction(Request $request, $reportId)
    {
        $report = Report::findOrFail($reportId);
        $action = $request->input('action'); // warned, suspended, banned, dismissed

        // Update the report status
        $report->update(['status' => $action]);

        // If suspending or banning — also update the actual user's account status
        if ($action === 'suspended' || $action === 'banned') {
            if ($report->reportedUser) {
                $report->reportedUser->update(['status' => $action]);
            }
        }

        return redirect()->route('admin.reports-flags')
            ->with('success', 'Report has been ' . $action . '.');
    }

    public function notifications_mark_read()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    // ===filter
    // public function sort(Request $request){
    //     $query = User::query();

    //     switch($request->sort){
    //         case 'az':
    //             $query->orderBy('first_name', 'asc');
    //             break;
    //         case 'za':
    //             $query->orderBy('first_name', 'desc');
    //             break;
    //         case 'youngest':
    //             $query->orderBy('created_at', 'desc');
    //             break;
    //         case 'oldest':
    //             $query->orderBy('created_at', 'asc');
    //             break;
    //     }

    //     $sorts = $query->get();
    //     return view('admin.user-management', compact('sorts'));
    // }
}