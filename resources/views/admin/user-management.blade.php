@extends('shared.view_main')

@section('content')
<div class="container">
    <h1>User Management</h1>
    <p>Welcome to your user management panel! Here you can manage your profile, view your pet's information, and access various features related to pet care.</p>

    <div class="stats_cards_section row">
        <div class="stats-card col-md-3">
            <h3>Total Users</h3>
            <h2>{{ $totalUsers ?? 0 }}</h2>
        </div>
        <div class="stats-card col-md-3">
            <h3>Active People</h3>
            <h2>{{ $activePeople ?? 0 }}</h2>
        </div>
        <div class="stats-card col-md-3">
            <h3>Inactive People</h3>
            <h2>{{ $inactivePeople ?? 0 }}</h2>
        </div>
    </div>

    {{-- ── Single form: sort + search always submitted together ── --}}
    <form method="GET" class="action-bar" action="{{ route('admin.user-management') }}" id="user-mgmt-form">
        <div class="actions">
            <label for="sort">Sort:</label>
            <select name="sort" id="sort-select" onchange="document.getElementById('user-mgmt-form').submit()">
                <option value="az"       {{ request('sort', 'az') == 'az'       ? 'selected' : '' }}>A to Z</option>
                <option value="za"       {{ request('sort') == 'za'             ? 'selected' : '' }}>Z to A</option>
                <option value="youngest" {{ request('sort') == 'youngest'       ? 'selected' : '' }}>Newest Members</option>
                <option value="oldest"   {{ request('sort') == 'oldest'         ? 'selected' : '' }}>Oldest Members</option>
            </select>
        </div>

        <div class="action2">
            <div class="search-bar">
                <label for="search">Search:</label>
                <input type="text" class="search" name="search"
                       value="{{ request('search') }}" placeholder="Search user...">
            </div>
            {{-- Filter removed, Search button only --}}
            <button type="submit" class="btn btn-secondary">Search</button>
        </div>
    </form>

    <br>

    <div class="user-list">
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Joined</th>
                    <th>Post(s)</th>
                    <th>Rehomed</th>
                    <th>Adopted</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    @if($user->role != 'admin')
                        <tr>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>{{ $user->created_at->format('F j, Y') }}</td>
                            <td>{{ $user->posts_count ?? 0 }}</td>
                            <td>{{ $user->rehomed_count ?? 0 }}</td>
                            <td>{{ $user->adopted_count ?? 0 }}</td>
                            <td>
                                @if($user->status == 'active')
                                        <span style="color: green;">Active</span>
                                    @else
                                        <span style="color: red;">Offline {{ $user->updated_at->diffInDays(now()) }}</span>
                                    @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection