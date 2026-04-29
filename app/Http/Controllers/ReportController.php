<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'reported_user_id' => 'nullable|exists:users,user_id',
            'reported_pet_id'  => 'nullable|exists:pets,pet_id',
            'report_type'      => 'required|string|max:100',
            'reason'           => 'required|string|max:1000',
        ]);

        $report = Report::create([
            'reporter_id'      => Auth::id(),
            'reported_user_id' => $request->reported_user_id,
            'reported_pet_id'  => $request->reported_pet_id,
            'report_type'      => $request->report_type,
            'reason'           => $request->reason,
            'status'           => 'pending',
        ]);

        // Notify all admins about the new report
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'      => $admin->user_id,
                'type'         => 'reported',
                'title'        => 'New Report Filed',
                'message'      => Auth::user()->first_name . ' ' . Auth::user()->last_name . ' filed a ' . $report->report_type . ' report.',
                'related_id'   => $report->report_id,
                'related_type' => 'report',
            ]);
        }

        return redirect()->back()->with('success', 'Report submitted successfully.');
    }
}