@extends('shared.view_main')

@section('content')
<div class="container">
    <div class="row">
        <h1>Dashboard</h1>
    </div>

    @auth
    <div class="welcome-header" style="color: white; background-color:#E68A2E">
        <h1>Welcome, {{ Auth::user()->username }}!</h1>
        <p>Here's a quick overview of your pet care activities and some useful features to explore.</p>
    </div>
    @endauth

    <div class="stats_cards_section row">
        <div class="stats-card col-md-2">
            <h3>Pets Available</h3>
            <h2>{{$petAvailable ?? 0}}</h2>
        </div>
        <div class="stats-card col-md-2">
            <h3>My Pending Requests</h3>
            <h2>{{$request ?? 0}}</h2>
        </div>
        <div class="stats-card col-md-2">
            <h3>Unread Messages</h3>
            <h2>{{$messages ?? 0}}</h2>
        </div>
        <div class="stats-card col-md-2">
            <h3>Rehommed Pets</h3>
            <h2>{{$rehomed ?? 0}}</h2>
        </div>
    </div>

    <div class="row">
        <br>
        <h1>Pets near you</h1>
        <p>Discover pets available for adoption in your area and find your new furry friend.</
        <div class="pet_cards col-md-12">
            @forelse($pets as $pet)
                <div class="pet-cards col-md-3">
                    <a href="{{route('petlover.pet-marketplace')}}" style="text-decoration: none;">
                        @include('others.pet-card', ['pet' => $pet])
                    </a>
                </div>
            @empty
                <div class="col-md-12">
                    <p>No furry friends available right now. Check back soon!</p>
                </div>
            @endforelse
        </div>
        @if($pets->hasPages())
            <div style="margin-top: 20px; width: 100%; height: 50px;">
                {{ $pets->appends(request()->except('dash_page'))->links() }}
            </div>
        @endif
    </div>
</div>
@endsection