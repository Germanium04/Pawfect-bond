@extends('shared.view_main')
@section('content')
<div class="container">
    <h1>Pet Marketplace</h1>
    <p>Welcome to the Pet Marketplace! Here you can find a variety of pets available for adoption or purchase. Browse through our listings and find your new furry friend today!</p>

    <div class="action-bar">
        <div class="actions">
            <label for="sort-select">Sort:</label>
            <select id="sort-select">
                <option value="newest_arrival">Newest Arrivals</option>
                <option value="longest_stay">Longest Stay (Needs a Home!)</option>
                <option value="age_desc">Youngest to Oldest</option>
                <option value="age_asc">Oldest to Youngest</option>
            </select>
        </div>

        <div class="action2">
            <div class="search-bar">
                <label for="search-input">Search:</label>
                <input type="text" class="search" id="search-input" placeholder="Search pet...">
            </div>
        </div>
    </div>

    <br>

    <div class="pet-listing" id="pet-listing">
        @forelse($pets as $pet)
            @php $med = $pet->medicalRecord; @endphp
            <div class="pet-cards col-md-3"
                 data-name              ="{{ strtolower($pet->name) }}"
                 data-created           ="{{ $pet->created_at->timestamp }}"
                 data-birthday          ="{{ \Carbon\Carbon::parse($pet->birthday)->timestamp }}"
                 data-vaccinated        ="{{ $med ? ($med->vaccinated ? 'Yes' : 'No') : 'Unknown' }}"
                 data-vaccinated-date   ="{{ $med?->vaccinated_date ? \Carbon\Carbon::parse($med->vaccinated_date)->format('M d, Y') : '—' }}"
                 data-vaccinated-cert   ="{{ $med?->vaccinated_certificate ? asset('storage/' . $med->vaccinated_certificate) : '' }}"
                 data-dewormed          ="{{ $med ? ($med->dewormed ? 'Yes' : 'No') : 'Unknown' }}"
                 data-dewormed-date     ="{{ $med?->dewormed_date ? \Carbon\Carbon::parse($med->dewormed_date)->format('M d, Y') : '—' }}"
                 data-dewormed-cert     ="{{ $med?->dewormed_certificate ? asset('storage/' . $med->dewormed_certificate) : '' }}"
                 data-neutered          ="{{ $med ? ($med->neutered ? 'Yes' : 'No') : 'Unknown' }}"
                 data-neutered-date     ="{{ $med?->neutered_date ? \Carbon\Carbon::parse($med->neutered_date)->format('M d, Y') : '—' }}"
                 data-neutered-cert     ="{{ $med?->neutered_certificate ? asset('storage/' . $med->neutered_certificate) : '' }}"
            >
                @include('others.pet-card', ['pet' => $pet])
            </div>
        @empty
    </div>
            <div class="col-md-12" style="text-align: center;">
                <p>No furry friends available right now. Check back soon!</p>
            </div>
        @endforelse

    <p id="no-results-msg" style="display:none; text-align:center; color:#a07050; margin-top:20px;">
        No pets match your search.
    </p>
</div>

<div id="pet-popup" class="pet-popup" style="display:none;">
    @include('others.pet-info-popup')
</div>
@endsection

@push('pet-info')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const listing     = document.getElementById('pet-listing');
    const sortSelect  = document.getElementById('sort-select');
    const searchInput = document.getElementById('search-input');
    const noResults   = document.getElementById('no-results-msg');

    function applyFilters() {
        const sortVal   = sortSelect.value;
        const searchVal = searchInput.value.trim().toLowerCase();

        const cards = Array.from(listing.querySelectorAll('.pet-cards'));

        // 1. Show/hide by search
        cards.forEach(function (card) {
            const name = card.dataset.name || '';
            card.style.display = (!searchVal || name.includes(searchVal)) ? '' : 'none';
        });

        // 2. Sort the visible cards
        const visible = cards.filter(c => c.style.display !== 'none');

        visible.sort(function (a, b) {
            switch (sortVal) {
                case 'newest_arrival':
                    return parseInt(b.dataset.created)  - parseInt(a.dataset.created);
                case 'longest_stay':
                    return parseInt(a.dataset.created)  - parseInt(b.dataset.created);
                case 'age_desc': // youngest first = latest birthday first
                    return parseInt(b.dataset.birthday) - parseInt(a.dataset.birthday);
                case 'age_asc':  // oldest first = earliest birthday first
                    return parseInt(a.dataset.birthday) - parseInt(b.dataset.birthday);
                default:
                    return 0;
            }
        });

        // Re-append in new order
        visible.forEach(c => listing.appendChild(c));

        // Empty state
        noResults.style.display = (visible.length === 0) ? 'block' : 'none';
    }

    sortSelect.addEventListener('change', applyFilters);
    searchInput.addEventListener('input', applyFilters);
});

// ── Popup ──────────────────────────────────────────────────────
function showPetInfo(el) {
    const popup = document.getElementById('pet-popup');
    if (!popup) return;

    document.getElementById('popup-name').innerText    = el.dataset.name     || '';
    document.getElementById('popup-age').value         = el.dataset.age      || '';
    document.getElementById('popup-gender').value      = el.dataset.gender   || '';
    document.getElementById('popup-breed').value       = el.dataset.breed    || '';
    document.getElementById('popup-address').value     = el.dataset.address  || '';
    document.getElementById('popup-like').value        = el.dataset.likes    || '';
    document.getElementById('popup-dislike').value     = el.dataset.dislikes || '';
    document.getElementById('popup-personality').value = el.dataset.personality || '';
    document.getElementById('popup-owner').value       = el.dataset.owner    || '';
    document.getElementById('popup-image').src         = el.dataset.image    || '';

    document.getElementById('popup-msg-btn').onclick = function () {
        window.location.href = '/pet-lover/community-inbox?with=' + el.dataset.ownerId;
    };

    document.getElementById('popup-pet-id').value = el.dataset.petId || '';
    window._popupOwnerId = el.dataset.ownerId || '';

    // ── Medical history table ──
    const card  = el.closest('.pet-cards') || el;
    const tbody = document.getElementById('popup-medical-body');
    tbody.innerHTML = '';

    const rows = [
        { label: 'Vaccination',       taken: card.dataset.vaccinated, date: card.dataset.vaccinatedDate, cert: card.dataset.vaccinatedCert },
        { label: 'Deworming',         taken: card.dataset.dewormed,   date: card.dataset.dewormedDate,   cert: card.dataset.dewormedCert   },
        { label: 'Spayed / Neutered', taken: card.dataset.neutered,   date: card.dataset.neuteredDate,   cert: card.dataset.neuteredCert   },
    ];

    rows.forEach(function (row) {
        const takenColor = row.taken === 'Yes' ? '#4CAF50' : (row.taken === 'No' ? '#FB7070' : '#aaa');
        const certCell   = row.cert
            ? `<a href="${row.cert}" target="_blank" style="color:#E68A2E; font-weight:bold;">View</a>`
            : '<span style="color:#aaa;">—</span>';

        tbody.innerHTML += `
            <tr>
                <td>${row.label}</td>
                <td style="color:${takenColor}; font-weight:bold;">${row.taken || '—'}</td>
                <td>${row.date || '—'}</td>
                <td>${certCell}</td>
            </tr>
        `;
    });

    popup.style.display = 'flex';
}

function closePetInfo() {
    document.getElementById('pet-popup').style.display = 'none';
}
</script>
@endpush