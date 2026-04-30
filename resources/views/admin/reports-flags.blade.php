@extends('shared.view_main')

@section('content')
<div class="container">
    <h1>Reports & Flags</h1>
    <p>Manage and take action on filed reports below.</p>

    <div class="stats_cards_section row">
        <div class="stats-card col-md-3">
            <h3>Total Reports</h3>
            <h2>{{ $reportCount ?? 0 }}</h2>
        </div>
        <div class="stats-card col-md-3">
            <h3>Pending</h3>
            <h2>{{ $pending ?? 0 }}</h2>
        </div>
        <div class="stats-card col-md-3">
            <h3>Suspended</h3>
            <h2>{{ $suspended ?? 0 }}</h2>
        </div>
        <div class="stats-card col-md-3">
            <h3>Banned</h3>
            <h2>{{ $banned ?? 0 }}</h2>
        </div>
    </div>

    {{-- ── Filters form ── --}}
    <form method="GET" action="{{ route('admin.reports-flags') }}" class="action-bar" id="reports-form">
        <div class="actions">
            <label>Sort:</label>
            <select name="sort" onchange="document.getElementById('reports-form').submit()">
                <option value="recent" {{ request('sort', 'recent') == 'recent' ? 'selected' : '' }}>Recent to Oldest</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest to Recent</option>
            </select>
        </div>

        <div class="action2">
            <div class="actions">
                <label>Type:</label>
                <select name="type" onchange="document.getElementById('reports-form').submit()">
                    <option value="All"  {{ request('type', 'All') == 'All'  ? 'selected' : '' }}>All</option>
                    <option value="post" {{ request('type') == 'post'        ? 'selected' : '' }}>Post</option>
                    <option value="user" {{ request('type') == 'user'        ? 'selected' : '' }}>User</option>
                </select>
            </div>

            <div class="actions">
                <label>Status:</label>
                <select name="status" onchange="document.getElementById('reports-form').submit()">
                    <option value="All"       {{ request('status', 'All') == 'All'      ? 'selected' : '' }}>All</option>
                    <option value="pending"   {{ request('status') == 'pending'         ? 'selected' : '' }}>Pending</option>
                    <option value="warned"    {{ request('status') == 'warned'          ? 'selected' : '' }}>Warned</option>
                    <option value="suspended" {{ request('status') == 'suspended'       ? 'selected' : '' }}>Suspended</option>
                    <option value="banned"    {{ request('status') == 'banned'          ? 'selected' : '' }}>Banned</option>
                    <option value="dismissed" {{ request('status') == 'dismissed'       ? 'selected' : '' }}>Dismissed</option>
                </select>
            </div>
        </div>
    </form>

    <br>

    {{-- ── Reports Mini Modals ── --}}
    <div class="board">
        <div class="adoption-card">
            @include('others.mini-modal', ['requests' => $report])
        </div>
    </div>

    @if($report->hasPages())
        <div style="margin-top: 16px; margin-bottom: 10px;">
            {{ $report->appends(request()->only(['sort','type','status']))->links() }}
        </div>
    @endif

</div>
@endsection