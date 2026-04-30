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
    </div>

    {{-- ══ ROW 1: Charts ══ --}}
    <div class="dash-row">

        {{-- Report Status Pie Chart --}}
        <div class="dash-panel" style="flex:1; min-width:280px;">
            <div class="dash-panel-header">
                <span>📋</span> Report Status Breakdown
            </div>
            <div style="padding:20px; display:flex; flex-direction:column; align-items:center; min-height:220px;">
                <canvas id="reportPieChart" width="200" height="200" style="max-width:200px;"></canvas>
                <div id="report-legend" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:14px;justify-content:center;"></div>
            </div>
        </div>

        {{-- Pet Listing Bar Chart --}}
        <div class="dash-panel" style="flex:1; min-width:280px;">
            <div class="dash-panel-header">
                <span>🐾</span> Pet Listing Overview
            </div>
            <div style="padding:20px; display:flex; justify-content:center; align-items:center; min-height:220px;">
                <canvas id="petBarChart" width="300" height="200" style="max-width:300px;"></canvas>
            </div>
        </div>

    </div>

    {{-- ══ TOP 3 USERS ══ --}}
    <div class="dash-panel" style="margin-top:30px;">
        <div class="dash-panel-header">
            <span>🏆</span> Top Users by Activity
            <a href="{{ route('admin.user-management') }}" class="dash-view-all">View All →</a>
        </div>
        <table class="table" style="width:100%;">
            <thead>
                <tr>
                    <th>Rank</th><th>User</th><th>Email</th>
                    <th>Posts</th><th>Rehomed</th><th>Adopted</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topUsers->take(3) as $i => $u)
                    <tr>
                        <td style="color:#FFC570; font-weight:bold; font-size:18px;">
                            @if($i === 0) 🥇 @elseif($i === 1) 🥈 @elseif($i === 2) 🥉 @endif
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
                    <tr><td colspan="7" style="color:#a07050; text-align:center; padding:20px;">No users yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ══ RECENT REPORTS TABLE — JS pagination + date range + export ══ --}}
    <div class="dash-panel" style="margin-top:30px; margin-bottom:40px;">
        <div class="dash-panel-header" style="flex-wrap:wrap; gap:8px;">
            <span>🚨</span> Recent Reports

            {{-- Date range filter --}}
            <div style="display:flex;align-items:center;gap:6px;margin-left:auto;">
                <label style="color:#FFF1DC;font-size:12px;font-weight:normal;">From:</label>
                <input type="date" id="date-from"
                       style="border-radius:8px;border:none;padding:3px 8px;font-size:12px;">
                <label style="color:#FFF1DC;font-size:12px;font-weight:normal;">To:</label>
                <input type="date" id="date-to"
                       style="border-radius:8px;border:none;padding:3px 8px;font-size:12px;">
                <button onclick="applyDateFilter()"
                        style="background:#4A2B0F;color:white;border:none;border-radius:8px;padding:4px 10px;font-size:12px;cursor:pointer;">
                    Filter
                </button>
                <button onclick="clearDateFilter()"
                        style="background:#888;color:white;border:none;border-radius:8px;padding:4px 10px;font-size:12px;cursor:pointer;">
                    Clear
                </button>
            </div>

            {{-- Export button --}}
            <button onclick="exportDashboardPDF()"
                    style="background:#4A2B0F;color:white;border:none;border-radius:8px;padding:5px 14px;font-size:13px;cursor:pointer;white-space:nowrap;">
                ⬇ Export PDF
            </button>
        </div>

        <table class="table" style="width:100%;">
            <thead>
                <tr>
                    <th>#</th><th>Reported By</th><th>Target</th>
                    <th>Type</th><th>Reason</th><th>Status</th><th>Date</th>
                </tr>
            </thead>
            <tbody id="recent-reports-tbody"></tbody>
        </table>

        <div id="report-pagination"></div>
    </div>
</div>{{-- /.container --}}

{{-- ══════════════════════════════════════════════════════════════
     HIDDEN PRINT / PDF EXPORT AREA
     This sits OUTSIDE .container so it can be the only thing
     visible during window.print().  The @media print rule below
     hides everything except this div.
════════════════════════════════════════════════════════════════ --}}
<div id="export-print-area">

    {{-- ── Print-only styles injected inline so they are always available ── --}}
    <style>
        /* ── Hide the export area in normal view ───────────────── */
        #export-print-area {
            display : none;
        }

        /* ── When printing: show ONLY the export area ──────────── */
        @media print {
            /* Hide the entire <body> content first */
            body * {
                visibility: hidden !important;
            }
            /* Then show only our export area and its children */
            #export-print-area,
            #export-print-area * {
                visibility: visible !important;
            }
            /* Position the export area to fill the page */
            #export-print-area {
                display  : block !important;
                position : fixed !important;
                top      : 0 !important;
                left     : 0 !important;
                width    : 100% !important;
                z-index  : 99999 !important;
                background: #fff !important;
            }
        }

        /* ── Export area internal styles ───────────────────────── */
        #export-print-area {
            font-family : Georgia, 'Times New Roman', serif;
            color       : #1a1a1a;
            background  : #ffffff;
        }

        #export-inner {
            padding   : 48px 56px;
            max-width : 960px;
            margin    : auto;
        }

        /* Header */
        #export-inner .ep-header {
            text-align    : center;
            padding-bottom: 24px;
            border-bottom : 3px solid #E68A2E;
            margin-bottom : 32px;
        }
        #export-inner .ep-header h1 {
            font-size     : 30px;
            letter-spacing: 3px;
            margin        : 0 0 4px;
            color         : #4A2B0F;
        }
        #export-inner .ep-header p {
            margin    : 2px 0;
            font-size : 13px;
            color     : #777;
        }

        /* Section headings */
        #export-inner h2 {
            font-size     : 16px;
            color         : #4A2B0F;
            border-bottom : 2px solid #E68A2E;
            padding-bottom: 6px;
            margin        : 32px 0 14px;
        }

        /* Tables */
        #export-inner table {
            width          : 100%;
            border-collapse: collapse;
            font-size      : 13px;
            margin-bottom  : 8px;
        }
        #export-inner th {
            background : #E68A2E;
            color      : #ffffff;
            padding    : 9px 13px;
            text-align : left;
            font-weight: 700;
        }
        #export-inner td {
            padding      : 8px 13px;
            border-bottom: 1px solid #e8ddd0;
            vertical-align: top;
        }
        #export-inner tr:nth-child(even) td {
            background: #fff8ee;
        }

        /* Status badges in export */
        #export-inner .ep-badge {
            display      : inline-block;
            padding      : 2px 10px;
            border-radius: 20px;
            font-size    : 11px;
            font-weight  : 700;
            color        : white;
        }

        /* Footer */
        #export-inner .ep-footer {
            text-align : center;
            font-size  : 11px;
            color      : #aaa;
            margin-top : 48px;
            padding-top: 16px;
            border-top : 1px solid #e8ddd0;
        }

        /* Two-column summary grid */
        #export-inner .ep-summary-grid {
            display              : grid;
            grid-template-columns: 1fr 1fr;
            gap                  : 0;
        }
        #export-inner .ep-summary-grid td:first-child {
            font-weight: 600;
            color      : #4A2B0F;
            width      : 55%;
        }
    </style>

    <div id="export-inner">

        {{-- Header --}}
        <div class="ep-header">
            <h1>PAW-FECT BOND</h1>
            <p>Admin Analytics Report</p>
            <p id="export-date-label" style="font-size:12px; color:#aaa;"></p>
        </div>

        {{-- Platform Overview --}}
        <h2>Platform Overview</h2>
        <table>
            <thead>
                <tr><th>Metric</th><th>Value</th></tr>
            </thead>
            <tbody>
                <tr><td>Total Users</td>          <td>{{ $userCount ?? 0 }}</td></tr>
                <tr><td>Available Pets</td>        <td>{{ $petCount ?? 0 }}</td></tr>
                <tr><td>Total Reports</td>         <td>{{ $reportCount ?? 0 }}</td></tr>
                <tr><td>Rehomed Pets</td>          <td>{{ $rehomed ?? 0 }}</td></tr>
                <tr><td>New Users This Month</td>  <td>{{ $newUsersThisMonth ?? 0 }}</td></tr>
            </tbody>
        </table>

        {{-- Report Status Breakdown --}}
        <h2>Report Status Breakdown</h2>
        <table>
            <thead>
                <tr><th>Status</th><th>Count</th><th>Share</th></tr>
            </thead>
            <tbody>
                @php
                    $rBreakdown = [
                        ['Pending',   $reportPending   ?? 0, '#E68A2E'],
                        ['Warned',    $reportWarned    ?? 0, '#FFC570'],
                        ['Suspended', $reportSuspended ?? 0, '#FFFACD'],
                        ['Banned',    $reportBanned    ?? 0, '#FB7070'],
                        ['Dismissed', $reportDismissed ?? 0, '#888888'],
                    ];
                    $rTotal = max(array_sum(array_column($rBreakdown, 1)), 1);
                @endphp
                @foreach($rBreakdown as [$label, $count, $color])
                    <tr>
                        <td>
                            <span class="ep-badge" style="background:{{ $color }};">
                                {{ $label }}
                            </span>
                        </td>
                        <td><strong>{{ $count }}</strong></td>
                        <td>{{ round($count / $rTotal * 100) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pet Listing Overview --}}
        <h2>Pet Listing Overview</h2>
        <table>
            <thead>
                <tr><th>Status</th><th>Count</th><th>Share</th></tr>
            </thead>
            <tbody>
                @php
                    $pBreakdown = [
                        ['Available', $petAvailable ?? 0, '#4CAF50'],
                        ['Rehomed',   $rehomed      ?? 0, '#E68A2E'],
                        ['Removed',   $petRemoved   ?? 0, '#FB7070'],
                    ];
                    $pTotal = max(array_sum(array_column($pBreakdown, 1)), 1);
                @endphp
                @foreach($pBreakdown as [$label, $count, $color])
                    <tr>
                        <td>
                            <span class="ep-badge" style="background:{{ $color }};">
                                {{ $label }}
                            </span>
                        </td>
                        <td><strong>{{ $count }}</strong></td>
                        <td>{{ round($count / $pTotal * 100) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Top 10 Users --}}
        <h2>Top 10 Users by Activity</h2>
        <table>
            <thead>
                <tr>
                    <th>Rank</th><th>Name</th><th>Username</th>
                    <th>Email</th><th>Posts</th><th>Rehomed</th><th>Adopted</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topUsers as $i => $u)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $u->first_name }} {{ $u->last_name }}</td>
                        <td>@{{ $u->username }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->posts_count }}</td>
                        <td>{{ $u->rehomed_count }}</td>
                        <td>{{ $u->adopted_count }}</td>
                        <td>
                            <span class="ep-badge"
                                  style="background:{{ $u->status === 'active' ? '#4CAF50' : '#FB7070' }};">
                                {{ ucfirst($u->status ?? 'active') }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Recent Reports — filled by JS at export time --}}
        <h2>
            Recent Reports
            <span id="export-date-range-label"
                  style="font-size:13px; font-weight:normal; color:#777; margin-left:8px;"></span>
        </h2>
        <table id="export-reports-table">
            <thead>
                <tr>
                    <th>#</th><th>Reported By</th><th>Target</th>
                    <th>Type</th><th>Reason</th><th>Status</th><th>Date</th>
                </tr>
            </thead>
            <tbody id="export-reports-tbody">
                {{-- Filled by exportDashboardPDF() --}}
            </tbody>
        </table>

        {{-- Footer --}}
        <div class="ep-footer">
            Generated by Paw-fect Bond &bull;
            <span id="export-generated-date"></span>
        </div>

    </div>{{-- /#export-inner --}}
</div>{{-- /#export-print-area --}}


{{-- ══ SCRIPTS ══ --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
/* ── Raw report data from PHP ──────────────────────────────── */
const reportData = @json($recentReports->map(function($r) {
    $isPost = $r->report_type === 'post';
    return [
        'reporter'   => trim(optional($r->reporter)->first_name . ' ' . optional($r->reporter)->last_name) ?: '—',
        'target'     => $isPost && $r->reportedPet
                            ? '🐾 ' . $r->reportedPet->name
                            : ($r->reportedUser
                                ? '👤 ' . $r->reportedUser->first_name . ' ' . $r->reportedUser->last_name
                                : '—'),
        'type'       => ucfirst($r->report_type),
        'reason'     => \Illuminate\Support\Str::limit($r->reason, 45),
        'fullReason' => $r->reason,
        'status'     => $r->status,
        'date'       => $r->created_at->format('M d, Y'),
        'rawDate'    => $r->created_at->format('Y-m-d'),
    ];
}));

/* ── Pie: Report Status ─────────────────────────────────────── */
(function () {
    const labels = ['Pending', 'Warned', 'Suspended', 'Banned', 'Dismissed'];
    const values = [
        {{ $reportPending   ?? 0 }},
        {{ $reportWarned    ?? 0 }},
        {{ $reportSuspended ?? 0 }},
        {{ $reportBanned    ?? 0 }},
        {{ $reportDismissed ?? 0 }}
    ];
    const colors = ['#E68A2E', '#FFC570', '#FFFACD', '#FB7070', '#888'];

    new Chart(document.getElementById('reportPieChart').getContext('2d'), {
        type : 'pie',
        data : {
            labels,
            datasets: [{
                data            : values,
                backgroundColor : colors,
                borderWidth     : 2,
                borderColor     : '#2E2E2E'
            }]
        },
        options: {
            plugins  : { legend: { display: false } },
            responsive: false
        }
    });

    const legend = document.getElementById('report-legend');
    labels.forEach(function (l, i) {
        const span = document.createElement('span');
        span.style.cssText = 'display:inline-flex;align-items:center;gap:5px;font-size:12px;color:#FFF1DC;';
        span.innerHTML =
            '<span style="width:11px;height:11px;border-radius:50%;background:' + colors[i] + ';display:inline-block;"></span>'
            + l + ': <b>' + values[i] + '</b>';
        legend.appendChild(span);
    });
})();

/* ── Bar: Pet Listing ───────────────────────────────────────── */
(function () {
    new Chart(document.getElementById('petBarChart').getContext('2d'), {
        type : 'bar',
        data : {
            labels  : ['Available', 'Rehomed', 'Removed'],
            datasets: [{
                data           : [{{ $petAvailable ?? 0 }}, {{ $rehomed ?? 0 }}, {{ $petRemoved ?? 0 }}],
                backgroundColor: ['#4CAF50', '#E68A2E', '#FB7070'],
                borderRadius   : 8,
                borderSkipped  : false,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales : {
                y: { beginAtZero: true, ticks: { color: '#FFF1DC', stepSize: 1 }, grid: { color: 'rgba(255,255,255,0.05)' } },
                x: { ticks: { color: '#FFF1DC' }, grid: { display: false } }
            },
            responsive: false,
        }
    });
})();

/* ════════════════════════════════════════════════════════════
   RECENT REPORTS — JS pagination + date filter
════════════════════════════════════════════════════════════ */
const REPORTS_PER_PAGE = 5;
let filteredReports = [...reportData];
let currentPage     = 1;

const statusStyle = {
    pending  : 'background:#E68A2E;color:white',
    warned   : 'background:#FFC570;color:#2E2E2E',
    suspended: 'background:#FFFACD;color:#2E2E2E',
    banned   : 'background:#FB7070;color:white',
    dismissed: 'background:#888;color:white',
};

function renderReportsTable() {
    const tbody = document.getElementById('recent-reports-tbody');
    const total = Math.max(1, Math.ceil(filteredReports.length / REPORTS_PER_PAGE));
    if (currentPage > total) currentPage = total;

    const start = (currentPage - 1) * REPORTS_PER_PAGE;
    const slice = filteredReports.slice(start, start + REPORTS_PER_PAGE);

    tbody.innerHTML = slice.length === 0
        ? '<tr><td colspan="7" style="color:#a07050;text-align:center;padding:20px;">No reports in this range.</td></tr>'
        : slice.map(function (r, i) {
            const ss = statusStyle[r.status] || 'background:#888;color:white';
            return '<tr>'
                + '<td style="color:#aaa;">' + (start + i + 1) + '</td>'
                + '<td style="color:#FFF1DC;">' + r.reporter + '</td>'
                + '<td style="color:#FFC570;">' + r.target + '</td>'
                + '<td><span style="background:#FFC570;color:#2E2E2E;border-radius:20px;padding:2px 10px;font-size:12px;">' + r.type + '</span></td>'
                + '<td style="color:#ccc;font-size:13px;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + r.reason + '</td>'
                + '<td><span style="' + ss + ';border-radius:20px;padding:2px 10px;font-size:12px;font-weight:bold;">'
                    + r.status.charAt(0).toUpperCase() + r.status.slice(1) + '</span></td>'
                + '<td style="color:#aaa;font-size:12px;white-space:nowrap;">' + r.date + '</td>'
                + '</tr>';
        }).join('');

    renderReportsPagination(total);
}

function renderReportsPagination(total) {
    const pg = document.getElementById('report-pagination');
    pg.innerHTML = '';
    if (total <= 1) return;

    function btn(label, page, disabled, active) {
        const b      = document.createElement('button');
        b.textContent = label;
        b.disabled    = disabled;
        if (active)    b.classList.add('pg-active');
        if (disabled)  b.classList.add('pg-disabled');
        if (!disabled) b.addEventListener('click', function () {
            currentPage = page;
            renderReportsTable();
        });
        return b;
    }

    pg.appendChild(btn('«', 1,              currentPage === 1,     false));
    pg.appendChild(btn('‹', currentPage - 1, currentPage === 1,    false));

    /* sliding window — max 5 page numbers */
    const win   = 5;
    let   wStart = Math.max(1, currentPage - Math.floor(win / 2));
    let   wEnd   = Math.min(total, wStart + win - 1);
    if (wEnd - wStart < win - 1) wStart = Math.max(1, wEnd - win + 1);

    if (wStart > 1) {
        pg.appendChild(btn(1, 1, false, false));
        if (wStart > 2) {
            const d = document.createElement('button');
            d.textContent = '…'; d.disabled = true; d.classList.add('pg-disabled');
            pg.appendChild(d);
        }
    }
    for (let i = wStart; i <= wEnd; i++) pg.appendChild(btn(i, i, false, i === currentPage));
    if (wEnd < total) {
        if (wEnd < total - 1) {
            const d = document.createElement('button');
            d.textContent = '…'; d.disabled = true; d.classList.add('pg-disabled');
            pg.appendChild(d);
        }
        pg.appendChild(btn(total, total, false, false));
    }

    pg.appendChild(btn('›', currentPage + 1, currentPage === total, false));
    pg.appendChild(btn('»', total,            currentPage === total, false));
}

function applyDateFilter() {
    const from = document.getElementById('date-from').value;
    const to   = document.getElementById('date-to').value;
    filteredReports = reportData.filter(function (r) {
        if (from && r.rawDate < from) return false;
        if (to   && r.rawDate > to)   return false;
        return true;
    });
    currentPage = 1;
    renderReportsTable();
}

function clearDateFilter() {
    document.getElementById('date-from').value = '';
    document.getElementById('date-to').value   = '';
    filteredReports = [...reportData];
    currentPage = 1;
    renderReportsTable();
}

/* Kick off initial render */
renderReportsTable();

/* ════════════════════════════════════════════════════════════
   PDF EXPORT
   1. Fill in the #export-print-area with current filtered data
   2. Show it
   3. window.print()  →  @media print hides everything else
   4. Hide it again after printing
════════════════════════════════════════════════════════════ */
function exportDashboardPDF() {
    /* ── Date labels ── */
    const now       = new Date();
    const formatted = now.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    document.getElementById('export-generated-date').textContent = formatted;
    document.getElementById('export-date-label').textContent     = 'Generated on ' + formatted;

    const from = document.getElementById('date-from').value;
    const to   = document.getElementById('date-to').value;
    let rangeLabel = '';
    if (from || to) rangeLabel = '(' + (from || '…') + ' → ' + (to || '…') + ')';
    document.getElementById('export-date-range-label').textContent = rangeLabel;

    /* ── Rebuild reports tbody from current filteredReports ── */
    const exportTbody = document.getElementById('export-reports-tbody');
    exportTbody.innerHTML = '';

    if (filteredReports.length === 0) {
        exportTbody.innerHTML =
            '<tr><td colspan="7" style="text-align:center;color:#a07050;padding:16px;">No reports in selected range.</td></tr>';
    } else {
        const badgeColors = {
            pending  : '#E68A2E',
            warned   : '#FFC570',
            suspended: '#FFFACD',
            banned   : '#FB7070',
            dismissed: '#888888',
        };
        const badgeText = {
            pending  : '#ffffff',
            warned   : '#2E2E2E',
            suspended: '#2E2E2E',
            banned   : '#ffffff',
            dismissed: '#ffffff',
        };

        filteredReports.forEach(function (r, i) {
            const bg  = badgeColors[r.status] || '#888';
            const txt = badgeText[r.status]   || '#fff';
            const tr  = document.createElement('tr');
            tr.innerHTML =
                '<td>' + (i + 1) + '</td>'
                + '<td>' + r.reporter + '</td>'
                + '<td>' + r.target   + '</td>'
                + '<td>' + r.type     + '</td>'
                + '<td>' + r.fullReason + '</td>'
                + '<td><span class="ep-badge" style="background:' + bg + ';color:' + txt + ';">'
                    + r.status.charAt(0).toUpperCase() + r.status.slice(1)
                + '</span></td>'
                + '<td>' + r.date + '</td>';
            exportTbody.appendChild(tr);
        });
    }

    /* ── Show export area, print, hide ── */
    const area = document.getElementById('export-print-area');
    area.style.display = 'block';

    /* Small delay lets the browser paint the area before print dialog */
    setTimeout(function () {
        window.print();
        /* Hide again after the print dialog is dismissed */
        area.style.display = 'none';
    }, 150);
}
</script>
@endsection