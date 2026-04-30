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
            <div class="col-md-12" style="text-align: center;">
                <p>No furry friends available right now. Check back soon!</p>
            </div>
        @endforelse
    </div>

    @if($pets->hasPages())
        <div style="margin-top: 20px; margin-bottom: 10px;">
            {{ $pets->appends(request()->only(['sort', 'search']))->links() }}
        </div>
    @endif

    <p id="no-results-msg" style="display:none; text-align:center; color:#a07050; margin-top:20px;">
        No pets match your search.
    </p>
</div>

<div id="pet-popup" class="pet-popup" style="display:none;">
    @include('others.pet-info-popup', ['isAdmin' => false])
</div>

@endsection

@push('pet-info')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const listing     = document.getElementById('pet-listing');
    const sortSelect  = document.getElementById('sort-select');
    const searchInput = document.getElementById('search-input');
    const noResults   = document.getElementById('no-results-msg');

    // ── Restore current filter state from URL ──
    const params = new URLSearchParams(window.location.search);
    if (params.get('sort'))   sortSelect.value  = params.get('sort');
    if (params.get('search')) searchInput.value = params.get('search');

    // ── Server-side navigation on sort change ──
    sortSelect.addEventListener('change', function () {
        submitFilters();
    });

    // ── Debounced search → server ──
    let searchTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(submitFilters, 400);
    });

    function submitFilters() {
        const url = new URL(window.location.href);
        url.searchParams.set('sort',   sortSelect.value);
        url.searchParams.set('search', searchInput.value.trim());
        url.searchParams.delete('page'); // reset to page 1 on new filter
        window.location.href = url.toString();
    }

    // ── Client-side visual filter for current page cards ──
    function applyClientFilter() {
        const searchVal = searchInput.value.trim().toLowerCase();
        const cards = Array.from(listing.querySelectorAll('.pet-cards'));

        cards.forEach(function (card) {
            const name = card.dataset.name || '';
            card.style.display = (!searchVal || name.includes(searchVal)) ? '' : 'none';
        });

        const visible = cards.filter(c => c.style.display !== 'none');
        noResults.style.display = (visible.length === 0 && searchVal) ? 'block' : 'none';
    }
});

function showPetInfo(el) {
    const popup = document.getElementById('pet-popup');
    if (!popup) return;

    document.getElementById('popup-name').innerText        = el.dataset.name        || '';
    document.getElementById('popup-age').value             = el.dataset.age         || '';
    document.getElementById('popup-gender').value          = el.dataset.gender      || '';
    document.getElementById('popup-breed').value           = el.dataset.breed       || '';
    document.getElementById('popup-address').value         = el.dataset.address     || '';
    document.getElementById('popup-like').value            = el.dataset.likes       || '';
    document.getElementById('popup-dislike').value         = el.dataset.dislikes    || '';
    document.getElementById('popup-personality').value     = el.dataset.personality || '';
    document.getElementById('popup-owner').value           = el.dataset.owner       || '';
    document.getElementById('popup-image').src             = el.dataset.image       || '';

    document.getElementById('popup-msg-btn').onclick = function () {
        window.location.href = '/pet-lover/community-inbox?with=' + el.dataset.ownerId;
    };

    document.getElementById('popup-pet-id').value = el.dataset.petId || '';
    window._popupOwnerId = el.dataset.ownerId || '';

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