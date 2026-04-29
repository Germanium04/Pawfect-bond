@extends('shared.view_main')

@section('content')
<div class="container">
    <h1>Adoption Tracker</h1>
    <p>Welcome to the Adoption Tracker! Here you can monitor the status of your adoption requests and see which pets are available for adoption.</p>

    <div class="action-bar">
        <div class="action2">
            <button type="button" id="btn-adopter" class="role-btn btn-secondary">Adopter</button>
            <button type="button" id="btn-rehome" class="role-btn btn-secondary">Rehome</button>
        </div>

        <div class="action2">
            <div id="filter-adopter" class="actions">
                <label>Category:</label>
                <select name="status">
                    <option value="All">All pets</option>
                    <option value="Pending">Pending</option>
                    <option value="Accepted">Accepted</option>
                </select>

                <label>Sort:</label>
                <select name="sort_adopter">
                    <option value="newest">Recent to Oldest</option>
                    <option value="oldest">Oldest to Recent</option>
                </select>
            </div>

            <div id="filter-rehome" class="actions">
                <label>Sort:</label>
                <select name="sort_rehome">
                    <option value="newest">Recent to Oldest</option>
                    <option value="oldest">Oldest to Recent</option>
                </select>
            </div>
        </div>
    </div>

    <br>

    <br>

    <div id="adopting-list" class="board">
        <div class="board-title"><span>My Adoption Requests</span></div>
        <br>
        <div class="adoption-card">
            {{-- Show requests where I am the applicant --}}
            @include('others.mini-modal', ['requests' => $sentRequests, 'role' => 'Adopter'])
            <br>
        </div>
    </div>

    <div id="rehome-list" class="board">
        <div class="board-title"><span>Requests for My Pets</span></div>
        <br>
        <div class="adoption-card">
            {{-- Show requests where I am the owner --}}
            @include('others.mini-modal', ['requests' => $receivedRequests, 'role' => 'Rehome'])
            <br>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnAdopter   = document.querySelector('#btn-adopter');
        const btnRehome    = document.querySelector('#btn-rehome');
        const filterAdopter = document.querySelector('#filter-adopter');
        const filterRehome  = document.querySelector('#filter-rehome');
        const listAdopting  = document.querySelector('#adopting-list');
        const listRehome    = document.querySelector('#rehome-list');

        // ── Filter + sort helper ──────────────────────────────
        function applyFilters(boardEl, statusVal, sortVal) {
            const cards = Array.from(boardEl.querySelectorAll('.minimodal'));

            // 1. Filter by status
            cards.forEach(function(card) {
                const cardStatus = card.dataset.status || '';
                if (statusVal === 'all') {
                    card.style.display = '';
                } else {
                    card.style.display = (cardStatus === statusVal) ? '' : 'none';
                }
            });

            // 2. Sort visible cards by date
            const visibleCards = cards.filter(c => c.style.display !== 'none');
            visibleCards.sort(function(a, b) {
                const da = new Date(a.dataset.date);
                const db = new Date(b.dataset.date);
                return sortVal === 'oldest' ? da - db : db - da;
            });

            // Re-append in sorted order inside the adoption-card wrapper
            const wrapper = boardEl.querySelector('.adoption-card');
            visibleCards.forEach(c => wrapper.appendChild(c));
        }

        // ── Wire Adopter dropdowns ────────────────────────────
        const statusSelect     = document.querySelector('#filter-adopter select[name="status"]');
        const sortAdopterSelect = document.querySelector('#filter-adopter select[name="sort_adopter"]');

        function runAdopterFilter() {
            const status = (statusSelect.value || 'all').toLowerCase();
            const sort   = sortAdopterSelect.value || 'newest';
            applyFilters(listAdopting, status, sort);
        }

        statusSelect.addEventListener('change', runAdopterFilter);
        sortAdopterSelect.addEventListener('change', runAdopterFilter);

        // ── Wire Rehome dropdown ──────────────────────────────
        const sortRehomeSelect = document.querySelector('#filter-rehome select[name="sort_rehome"]');

        sortRehomeSelect.addEventListener('change', function() {
            applyFilters(listRehome, 'all', this.value);
        });

        // ── Tab toggle ────────────────────────────────────────
        function toggleRole(role) {
            if (role === 'adopter') {
                filterAdopter.style.display = 'flex';
                listAdopting.style.display  = 'block';
                filterRehome.style.display  = 'none';
                listRehome.style.display    = 'none';
                btnAdopter.classList.add('active');
                btnRehome.classList.remove('active');
                runAdopterFilter(); // apply current filter on switch
            } else {
                filterRehome.style.display  = 'flex';
                listRehome.style.display    = 'block';
                filterAdopter.style.display = 'none';
                listAdopting.style.display  = 'none';
                btnRehome.classList.add('active');
                btnAdopter.classList.remove('active');
                applyFilters(listRehome, 'all', sortRehomeSelect.value || 'newest');
            }
        }

        toggleRole('adopter');
        btnAdopter.addEventListener('click', () => toggleRole('adopter'));
        btnRehome.addEventListener('click',  () => toggleRole('rehome'));
    });
</script>
@endpush
@endsection