@extends('shared.view_main')

@section('content')
<div class="container">
    <h1>Pet Listing</h1>
    <p>Welcome to your pet listing! Here you can manage your profile, view your pet's information, and access various features related to pet care.</p>

    <div class="stats_cards_section row">
        <div class="stats-card col-md-3">
            <h3>Total Listing</h3>
            <h2>{{ $totalListing ?? 0 }}</h2>
        </div>
        <div class="stats-card col-md-3">
            <h3>Available</h3>
            <h2>{{ $available ?? 0 }}</h2>
        </div>
        <div class="stats-card col-md-3">
            <h3>Removed</h3>
            <h2>{{ $removed ?? 0 }}</h2>
        </div>
    </div>

    <div class="action-bar" id="pet-listing-bar">
        <div class="actions">
            <label>Sort:</label>
            <select id="sort-select">
                <option value="newest_arrival" {{ request('sort', 'newest_arrival') == 'newest_arrival' ? 'selected' : '' }}>Newest Arrivals</option>
                <option value="longest_stay"   {{ request('sort') == 'longest_stay'   ? 'selected' : '' }}>Longest Stay</option>
                <option value="age_desc"       {{ request('sort') == 'age_desc'       ? 'selected' : '' }}>Youngest to Oldest</option>
                <option value="age_asc"        {{ request('sort') == 'age_asc'        ? 'selected' : '' }}>Oldest to Youngest</option>
            </select>
        </div>

        <div class="action2">
            <div class="search-bar">
                <label>Search:</label>
                <input type="text" class="search" id="search-input"
                       value="{{ request('search') }}" placeholder="Search pet...">
            </div>
            <button type="button" class="btn btn-secondary" id="search-btn">Search</button>
        </div>
    </div>

    <br>

    <div class="pet-grid" id="pet-grid">
        @forelse($pets as $pet)
            @include('others.pet-card', ['pet' => $pet])
        @empty
            <p class="no-pets-msg">No furry friends available right now. Check back soon!</p>
        @endforelse
    </div>

    @if($pets->hasPages())
        <div id="pet-pagination" style="margin-top: 20px; margin-bottom: 10px;">
            {{ $pets->appends(request()->only(['sort','search']))->links() }}
        </div>
    @endif
</div>

{{-- Admin Pet Info Popup --}}
<div id="admin-pet-popup-overlay" class="pet-popup" style="display:none;" onclick="if(event.target===this) closeAdminPetInfo()">
    @include('others.pet-info-popup', ['isAdmin' => true])
</div>

<script>
const baseUrl   = '{{ route("admin.pet-listing") }}';
const sortSel   = document.getElementById('sort-select');
const searchInp = document.getElementById('search-input');
const searchBtn = document.getElementById('search-btn');

function fetchPets() {
    const params = new URLSearchParams({
        sort:   sortSel.value,
        search: searchInp.value,
    });
    // Redirect to page 1 with new filters (server paginates)
    window.location.href = baseUrl + '?' + params.toString();
}

// Restore current filter values from URL
(function() {
    const p = new URLSearchParams(window.location.search);
    if (p.get('sort'))   sortSel.value  = p.get('sort');
    if (p.get('search')) searchInp.value = p.get('search');
})();

sortSel.addEventListener('change', fetchPets);
searchBtn.addEventListener('click', fetchPets);
searchInp.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); fetchPets(); }
});

function showAdminPetInfo(el) {
    document.getElementById('admin-popup-delete-form').action = `/admin/pet-listing/${el.dataset.petId}`;

    document.getElementById('admin-popup-image').src         = el.dataset.image;
    document.getElementById('admin-popup-name').textContent  = el.dataset.name;
    document.getElementById('admin-popup-age').value         = el.dataset.age;
    document.getElementById('admin-popup-gender').value      = el.dataset.gender;
    document.getElementById('admin-popup-breed').value       = el.dataset.breed;
    document.getElementById('admin-popup-address').value     = el.dataset.address;
    document.getElementById('admin-popup-like').value        = el.dataset.likes;
    document.getElementById('admin-popup-owner').value       = el.dataset.owner;
    document.getElementById('admin-popup-dislike').value     = el.dataset.dislikes;
    document.getElementById('admin-popup-personality').value = el.dataset.personality;

    const tbody = document.getElementById('admin-popup-medical-body');
    tbody.innerHTML = '';
    try {
        const med = JSON.parse(el.dataset.medical || '[]');
        med.forEach(row => {
            tbody.innerHTML += '<tr><td>'+row.type+'</td><td>'+(row.taken?'Yes':'No')+'</td><td>'+(row.date||'—')+'</td><td>'+(row.cert?'<a href="'+row.cert+'" target="_blank">View</a>':'—')+'</td></tr>';
        });
    } catch(e) {}

    document.getElementById('admin-pet-popup-overlay').style.display = 'flex';
}

function closeAdminPetInfo() {
    document.getElementById('admin-pet-popup-overlay').style.display = 'none';
}
</script>
@endsection