@extends('shared.view_main')

@section('content')
<div class="container">
    <div class="row">
        <h1>Dashboard</h1>
    </div>

    @auth
        <div class="welcome-header" style="color: white; background-color:#E68A2E">
            <h1>Welcome, Admin {{ Auth::user()->username }}!</h1>
            <p>Here's a quick overview of your platform activity and analytics.</p>
        </div>
    @endauth

    {{-- ══ STATS CARDS ══ --}}
    <div class="stats_cards_section row">
        <div class="stats-card col-md-3">
            <h3>Total Users</h3>
            <h2>{{ $userCount ?? 0 }}</h2>
        </div>
        <div class="stats-card col-md-3">
            <h3>Available Pets</h3>
            <h2>{{ $petCount ?? 0 }}</h2>
        </div>
        <div class="stats-card col-md-3">
            <h3>Total Reports</h3>
            <h2>{{ $reportCount ?? 0 }}</h2>
        </div>
        <div class="stats-card col-md-3">
            <h3>Rehomed Pets</h3>
            <h2>{{ $rehomed ?? 0 }}</h2>
        </div>
        <!-- <div class="stats-card col-md-3">
            <h3>New Users This Month</h3>
            <h2>{{ $newUsersThisMonth ?? 0 }}</h2>
        </div>
        <div class="stats-card col-md-3">
            <h3>Pending Reports</h3>
            <h2>{{ $reportPending ?? 0 }}</h2>
        </div> -->
    </div>

    {{-- ══ ROW 1: Breakdowns ══ --}}
    <div class="dash-row">

        <div class="dash-panel" style="flex:1; min-width:280px;">
            <div class="dash-panel-header">
                <span>📋</span> Report Status Breakdown
            </div>
            <table class="table" style="width:100%;">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Count</th>
                        <th>Share</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $breakdown = [
                            ['label' => 'Pending',   'count' => $reportPending,   'color' => '#E68A2E', 'dark' => false],
                            ['label' => 'Warned',    'count' => $reportWarned,    'color' => '#FFC570', 'dark' => true],
                            ['label' => 'Suspended', 'count' => $reportSuspended, 'color' => '#FFFACD', 'dark' => true],
                            ['label' => 'Banned',    'count' => $reportBanned,    'color' => '#FB7070', 'dark' => false],
                            ['label' => 'Dismissed', 'count' => $reportDismissed, 'color' => '#888',    'dark' => false],
                        ];
                        $totalR = max(array_sum(array_column($breakdown, 'count')), 1);
                    @endphp
                    @foreach($breakdown as $row)
                        @php $pct = round($row['count'] / $totalR * 100); @endphp
                        <tr>
                            <td>
                                <span class="dash-status-badge" style="background:{{ $row['color'] }}; color:{{ $row['dark'] ? '#2E2E2E' : 'white' }};">
                                    {{ $row['label'] }}
                                </span>
                            </td>
                            <td style="color:white; font-weight:bold;">{{ $row['count'] }}</td>
                            <td>
                                <div class="dash-bar-wrap">
                                    <div class="dash-bar" style="width:{{ $pct }}%; background:{{ $row['color'] }};"></div>
                                </div>
                                <span style="color:#aaa; font-size:12px;">{{ $pct }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="dash-panel" style="flex:1; min-width:280px;">
            <div class="dash-panel-header">
                <span>🐾</span> Pet Listing Overview
            </div>
            <table class="table" style="width:100%;">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Count</th>
                        <th>Share</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $petBreakdown = [
                            ['label' => 'Available', 'count' => $petAvailable, 'color' => '#4CAF50'],
                            ['label' => 'Rehomed',   'count' => $rehomed,      'color' => '#E68A2E'],
                            ['label' => 'Removed',   'count' => $petRemoved,   'color' => '#FB7070'],
                        ];
                        $totalP = max(array_sum(array_column($petBreakdown, 'count')), 1);
                    @endphp
                    @foreach($petBreakdown as $row)
                        @php $pct = round($row['count'] / $totalP * 100); @endphp
                        <tr>
                            <td>
                                <span class="dash-status-badge" style="background:{{ $row['color'] }}; color:white;">
                                    {{ $row['label'] }}
                                </span>
                            </td>
                            <td style="color:white; font-weight:bold;">{{ $row['count'] }}</td>
                            <td>
                                <div class="dash-bar-wrap">
                                    <div class="dash-bar" style="width:{{ $pct }}%; background:{{ $row['color'] }};"></div>
                                </div>
                                <span style="color:#aaa; font-size:12px;">{{ $pct }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    {{-- ══ RECENT REPORTS TABLE ══ --}}
    <div class="dash-panel" style="margin-top:30px;">
        <div class="dash-panel-header">
            <span>🚨</span> Recent Reports
            <a href="{{ route('admin.reports-flags') }}" class="dash-view-all">View All →</a>
        </div>
        <table class="table" style="width:100%;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Reported By</th>
                    <th>Target</th>
                    <th>Type</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentReports as $i => $r)
                    <tr>
                        <td style="color:#aaa;">{{ $i + 1 }}</td>
                        <td style="color:#FFF1DC;">
                            {{ $r->reporter?->first_name ?? '—' }} {{ $r->reporter?->last_name ?? '' }}
                        </td>
                        <td style="color:#FFC570;">
                            @if($r->report_type === 'post' && $r->reportedPet)
                                🐾 {{ $r->reportedPet->name }}
                            @elseif($r->reportedUser)
                                👤 {{ $r->reportedUser->first_name }} {{ $r->reportedUser->last_name }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <span class="dash-type-badge {{ $r->report_type === 'post' ? 'dash-type-post' : 'dash-type-user' }}">
                                {{ ucfirst($r->report_type) }}
                            </span>
                        </td>
                        <td style="color:#ccc; font-size:13px; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ Str::limit($r->reason, 45) }}
                        </td>
                        <td>
                            <span class="dash-status-badge dash-status-{{ $r->status }}">
                                {{ ucfirst($r->status) }}
                            </span>
                        </td>
                        <td style="color:#aaa; font-size:12px; white-space:nowrap;">
                            {{ $r->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="color:#a07050; text-align:center; padding:20px;">
                            No reports yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ══ TOP USERS TABLE ══ --}}
    <div class="dash-panel" style="margin-top:30px; margin-bottom:40px;">
        <div class="dash-panel-header">
            <span>🏆</span> Top Users by Activity
            <a href="{{ route('admin.user-management') }}" class="dash-view-all">View All →</a>
        </div>
        <table class="table" style="width:100%;">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Posts</th>
                    <th>Rehomed</th>
                    <th>Adopted</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topUsers as $i => $u)
                    <tr>
                        <td style="color:#FFC570; font-weight:bold; font-size:18px;">
                            @if($i === 0) 🥇
                            @elseif($i === 1) 🥈
                            @elseif($i === 2) 🥉
                            @else <span style="font-size:14px; color:#aaa;">{{ $i + 1 }}</span>
                            @endif
                        </td>
                        <td style="color:#FFF1DC;">
                            {{ $u->first_name }} {{ $u->last_name }}
                            <br><span style="color:#aaa; font-size:12px;">&#64;{{ $u->username }}</span>
                        </td>
                        <td style="color:#aaa; font-size:13px;">{{ $u->email }}</td>
                        <td style="color:#FFC570; font-weight:bold; text-align:center;">{{ $u->posts_count }}</td>
                        <td style="color:#4CAF50; font-weight:bold; text-align:center;">{{ $u->rehomed_count }}</td>
                        <td style="color:#E68A2E; font-weight:bold; text-align:center;">{{ $u->adopted_count }}</td>
                        <td>
                            <span style="border-radius:20px; padding:3px 14px; font-size:12px; font-weight:bold;
                                background:{{ $u->status === 'active' ? '#4CAF50' : '#FB7070' }}; color:white;">
                                {{ ucfirst($u->status ?? 'active') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="color:#a07050; text-align:center; padding:20px;">
                            No users yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection