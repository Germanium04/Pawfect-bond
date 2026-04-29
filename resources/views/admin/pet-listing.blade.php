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
</div>

{{-- Admin Pet Info Popup (view-only) --}}
<div id="admin-pet-popup-overlay" class="pet-popup" style="display:none;" onclick="if(event.target===this) closeAdminPetInfo()">
    <div class="pet-info-pop" style="position:relative; overflow-y:auto;">
        <span class="close-btn" onclick="closeAdminPetInfo()">×</span>
        <div class="pet-about">
            <h2 style="text-align:center; margin:10px auto 0 auto; font-size:24px;">PET INFORMATION</h2>
            <div class="pp-row1">
                <div class="pet-pic-pp"><img id="admin-popup-image" src="" alt="pet"></div>
                <div class="pet-name-pp">
                    <h2 id="admin-popup-name"></h2>
                    <hr class="pp-name">
                    <label>Pet Name</label>
                </div>
            </div>
            <div class="pop-top">
                <div class="pet-mini-inf"><label>Age:</label><input id="admin-popup-age" class="pop" readonly></div>
                <div class="pet-mini-inf"><label>Gender:</label><input id="admin-popup-gender" class="pop" readonly></div>
                <div class="pet-mini-inf"><label>Breed:</label><input id="admin-popup-breed" class="pop" readonly></div>
                <div class="pet-mini-inf"><label>Address:</label><input id="admin-popup-address" class="pop" readonly></div>
            </div>
            <hr style="width:90%; margin:15px auto; border:2px solid black; border-radius:5px;">
            <div class="pop-top">
                <div class="pet-mini-inf"><label>Likes:</label><input id="admin-popup-like" class="pop" readonly></div>
                <div class="pet-mini-inf"><label>Owner:</label><input id="admin-popup-owner" class="pop" readonly></div>
                <div class="pet-mini-inf"><label>Dislike:</label><input id="admin-popup-dislike" class="pop" readonly></div>
            </div>
            <div class="pop-lower">
                <label>Personality:</label>
                <input class="pop" id="admin-popup-personality" readonly>
            </div>
            <div style="margin: 50px 25px 10px 25px;">
                <label>Medical History:</label>
                <table class="medical-table">
                    <thead>
                        <tr><th>Medical Record</th><th>Taken</th><th>Latest Date</th><th>Certificate</th></tr>
                    </thead>
                    <tbody id="admin-popup-medical-body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const baseUrl   = '{{ route("admin.pet-listing") }}';
const sortSel   = document.getElementById('sort-select');
const searchInp = document.getElementById('search-input');
const searchBtn = document.getElementById('search-btn');
const grid      = document.getElementById('pet-grid');

function fetchPets() {
    const params = new URLSearchParams({
        sort:   sortSel.value,
        search: searchInp.value,
    });

    history.replaceState(null, '', baseUrl + '?' + params.toString());

    grid.style.transition = 'opacity 0.2s';
    grid.style.opacity    = '0.4';

    fetch(baseUrl + '?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.text())
    .then(html => {
        const parser  = new DOMParser();
        const doc     = parser.parseFromString(html, 'text/html');
        const newGrid = doc.getElementById('pet-grid');
        grid.innerHTML = newGrid ? newGrid.innerHTML : '';
        grid.style.opacity = '1';
    })
    .catch(() => { grid.style.opacity = '1'; });
}

sortSel.addEventListener('change', fetchPets);
searchBtn.addEventListener('click', fetchPets);
searchInp.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); fetchPets(); }
});

function showAdminPetInfo(el) {
    document.getElementById('admin-popup-image').src        = el.dataset.image;
    document.getElementById('admin-popup-name').textContent = el.dataset.name;
    document.getElementById('admin-popup-age').value        = el.dataset.age;
    document.getElementById('admin-popup-gender').value     = el.dataset.gender;
    document.getElementById('admin-popup-breed').value      = el.dataset.breed;
    document.getElementById('admin-popup-address').value    = el.dataset.address;
    document.getElementById('admin-popup-like').value       = el.dataset.likes;
    document.getElementById('admin-popup-owner').value      = el.dataset.owner;
    document.getElementById('admin-popup-dislike').value    = el.dataset.dislikes;
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