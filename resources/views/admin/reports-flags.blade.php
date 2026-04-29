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

    {{-- ── All dropdowns in ONE form so values persist together ── --}}
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
                    <option value="All"       {{ request('status', 'All') == 'All'   ? 'selected' : '' }}>All</option>
                    <option value="pending"   {{ request('status') == 'pending'      ? 'selected' : '' }}>Pending</option>
                    <option value="warned"    {{ request('status') == 'warned'       ? 'selected' : '' }}>Warned</option>
                    <option value="suspended" {{ request('status') == 'suspended'    ? 'selected' : '' }}>Suspended</option>
                    <option value="banned"    {{ request('status') == 'banned'       ? 'selected' : '' }}>Banned</option>
                    <option value="dismissed" {{ request('status') == 'dismissed'    ? 'selected' : '' }}>Dismissed</option>
                </select>
            </div>
        </div>
    </form>

    <br>

    {{-- ── Report cards styled like Image 2 ── --}}
    <div class="rf-list">
        @forelse($report as $item)
            @php
                $status = strtolower($item->status);
                $isPost = $item->report_type === 'post';

                // Image
                if ($isPost && $item->reportedPet) {
                    $img = asset('storage/' . $item->reportedPet->pet_image);
                } elseif ($item->reportedUser && $item->reportedUser->profile_image) {
                    $img = asset('storage/' . $item->reportedUser->profile_image);
                } else {
                    $img = asset('assets/profile.png');
                }

                // Title
                if ($isPost && $item->reportedPet) {
                    $title    = $item->reportedPet->name;
                    $subtitle = $item->reportedPet->age;
                } elseif ($item->reportedUser) {
                    $title    = $item->reportedUser->first_name . ' ' . $item->reportedUser->last_name;
                    $subtitle = ($item->reportedUser->posts_count ?? 0) . ' post';
                } else {
                    $title    = 'Unknown';
                    $subtitle = '';
                }

                $typeLabel = ucfirst($item->report_type);
            @endphp

            <div class="rf-card {{ $status !== 'pending' ? 'rf-card--actioned' : '' }}">

                {{-- Left: avatar --}}
                <img src="{{ $img }}" alt="{{ $title }}" class="rf-avatar">

                {{-- Middle: info --}}
                <div class="rf-info">
                    <span class="rf-name">{{ $title }}</span>
                    <span class="rf-sub">{{ $subtitle }}</span>
                    <span class="rf-badge">{{ $typeLabel }}</span>
                </div>

                {{-- Right: action buttons OR status badge --}}
                <div class="rf-actions">
                    @if($status === 'pending')
                        <form method="POST" action="{{ route('admin.report-action', $item->report_id) }}"
                              onsubmit="return confirm('Warn this user?')">
                            @csrf @method('PATCH')
                            <input type="hidden" name="action" value="warned">
                            <button type="submit" class="rf-btn rf-btn--warn">Warn</button>
                        </form>

                        <form method="POST" action="{{ route('admin.report-action', $item->report_id) }}"
                              onsubmit="return confirm('Suspend this user?')">
                            @csrf @method('PATCH')
                            <input type="hidden" name="action" value="suspended">
                            <button type="submit" class="rf-btn rf-btn--suspend">Suspend</button>
                        </form>

                        <form method="POST" action="{{ route('admin.report-action', $item->report_id) }}"
                              onsubmit="return confirm('Ban this user?')">
                            @csrf @method('PATCH')
                            <input type="hidden" name="action" value="banned">
                            <button type="submit" class="rf-btn rf-btn--ban">Ban</button>
                        </form>

                        <form method="POST" action="{{ route('admin.report-action', $item->report_id) }}"
                              onsubmit="return confirm('Dismiss this report?')">
                            @csrf @method('PATCH')
                            <input type="hidden" name="action" value="dismissed">
                            <button type="submit" class="rf-btn rf-btn--dismiss">Dismiss</button>
                        </form>

                    @elseif($status === 'suspended')
                        <span class="rf-status-badge rf-status--suspended">Suspended</span>

                    @elseif($status === 'banned')
                        <span class="rf-status-badge rf-status--banned">Banned</span>

                    @elseif($status === 'warned')
                        <span class="rf-status-badge rf-status--warned">Warned</span>

                    @elseif($status === 'dismissed')
                        <span class="rf-status-badge rf-status--dismissed">Dismissed</span>
                    @endif
                </div>
            </div>
        @empty
            <p style="color:#a07050; padding:20px; text-align:center;">No reports found.</p>
        @endforelse
    </div>
</div>

@endsection