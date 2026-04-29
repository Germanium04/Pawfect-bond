{{-- The nav-bar wrapper div lives in view_main.blade.php (id="sidebar") --}}
{{-- Do NOT add another .nav-bar div here --}}

<div class="logo-nav">
    <img src="{{ asset('assets/logo.png') }}" alt="Logo">
</div>

<hr>
<br>

<div class="nav-links">
    @auth
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" wire:navigate>Dashboard</a>
            <a href="{{ route('admin.user-management') }}" wire:navigate>User Management</a>
            <a href="{{ route('admin.pet-listing') }}" wire:navigate>Pet Listing</a>
            <a href="{{ route('admin.reports-flags') }}" wire:navigate>Reports & Flags</a>
        @elseif(auth()->user()->role === 'pet_lover')
            <a href="{{ route('petlover.dashboard') }}" wire:navigate>Dashboard</a>
            <a href="{{ route('petlover.pet-marketplace') }}" wire:navigate>Pet Marketplace</a>
            <a href="{{ route('petlover.rehoming-center') }}" wire:navigate>Rehoming Center</a>
            <a href="{{ route('petlover.adoption-tracker') }}" wire:navigate>Adoption Tracker</a>
            <a href="{{ route('petlover.community-inbox') }}" wire:navigate>Community Inbox</a>
        @endif
    @endauth
</div>

<br>
<hr>

<div class="sign-out">
    @auth
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-secondary">Sign Out</button>
        </form>
    @endauth
</div>