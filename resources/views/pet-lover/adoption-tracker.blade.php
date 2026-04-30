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
                    <option value="all">All pets</option>
                    <option value="pending">Pending</option>
                    <option value="accepted">Accepted</option>
                    <option value="rejected">Declined</option>
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
        const btnAdopter    = document.querySelector('#btn-adopter');
        const btnRehome     = document.querySelector('#btn-rehome');
        const filterAdopter = document.querySelector('#filter-adopter');
        const filterRehome  = document.querySelector('#filter-rehome');
        const listAdopting  = document.querySelector('#adopting-list');
        const listRehome    = document.querySelector('#rehome-list');

        const PER_PAGE = 5;

        // ── Pagination renderer ────────────────────────────────
        function renderPagination(boardEl, totalPages, currentPage, onPageChange) {
            let pg = boardEl.querySelector('.at-pagination');
            if (!pg) {
                pg = document.createElement('div');
                pg.className = 'at-pagination';
                boardEl.querySelector('.adoption-card').after(pg);
            }
            pg.innerHTML = '';
            if (totalPages <= 1) return;

            function makeBtn(label, page, disabled, active) {
                const b = document.createElement('button');
                b.textContent = label;
                b.disabled = disabled;
                if (active)   b.classList.add('pg-active');
                if (disabled) b.classList.add('pg-disabled');
                if (!disabled) b.addEventListener('click', () => onPageChange(page));
                return b;
            }

            pg.appendChild(makeBtn('«', 1, currentPage === 1, false));
            pg.appendChild(makeBtn('‹', currentPage - 1, currentPage === 1, false));
            for (let i = 1; i <= totalPages; i++) {
                pg.appendChild(makeBtn(i, i, false, i === currentPage));
            }
            pg.appendChild(makeBtn('›', currentPage + 1, currentPage === totalPages, false));
            pg.appendChild(makeBtn('»', totalPages, currentPage === totalPages, false));
        }

        // ── Filter + sort + paginate helper ──────────────────
        function applyFilters(boardEl, statusVal, sortVal, page) {
            page = page || 1;
            const allCards = Array.from(boardEl.querySelectorAll('.minimodal'));

            // 1. Filter by status
            const filtered = allCards.filter(function(card) {
                const cardStatus = (card.dataset.status || '').toLowerCase();
                return statusVal === 'all' || cardStatus === statusVal;
            });

            // 2. Sort
            filtered.sort(function(a, b) {
                const da = new Date(a.dataset.date);
                const db = new Date(b.dataset.date);
                return sortVal === 'oldest' ? da - db : db - da;
            });

            // 3. Hide all
            allCards.forEach(c => c.style.display = 'none');

            // 4. Paginate
            const totalPages = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
            const start = (page - 1) * PER_PAGE;
            const pageCards = filtered.slice(start, start + PER_PAGE);
            pageCards.forEach(c => c.style.display = '');

            // 5. Re-append in sorted order
            const wrapper = boardEl.querySelector('.adoption-card');
            filtered.forEach(c => wrapper.appendChild(c));

            // 6. Pagination UI
            renderPagination(boardEl, totalPages, page, function(newPage) {
                const sv = boardEl === listAdopting
                    ? (document.querySelector('#filter-adopter select[name="status"]').value || 'all').toLowerCase()
                    : 'all';
                const so = boardEl === listAdopting
                    ? (document.querySelector('#filter-adopter select[name="sort_adopter"]').value || 'newest')
                    : (document.querySelector('#filter-rehome select[name="sort_rehome"]').value || 'newest');
                applyFilters(boardEl, sv, so, newPage);
            });
        }

        // ── Wire Adopter dropdowns ────────────────────────────
        const statusSelect      = document.querySelector('#filter-adopter select[name="status"]');
        const sortAdopterSelect = document.querySelector('#filter-adopter select[name="sort_adopter"]');

        function runAdopterFilter(page) {
            const status = (statusSelect.value || 'all').toLowerCase();
            const sort   = sortAdopterSelect.value || 'newest';
            applyFilters(listAdopting, status, sort, page || 1);
        }

        statusSelect.addEventListener('change',      () => runAdopterFilter(1));
        sortAdopterSelect.addEventListener('change', () => runAdopterFilter(1));

        // ── Wire Rehome dropdown ──────────────────────────────
        const sortRehomeSelect = document.querySelector('#filter-rehome select[name="sort_rehome"]');

        sortRehomeSelect.addEventListener('change', function() {
            applyFilters(listRehome, 'all', this.value, 1);
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
                runAdopterFilter(1);
            } else {
                filterRehome.style.display  = 'flex';
                listRehome.style.display    = 'block';
                filterAdopter.style.display = 'none';
                listAdopting.style.display  = 'none';
                btnRehome.classList.add('active');
                btnAdopter.classList.remove('active');
                applyFilters(listRehome, 'all', sortRehomeSelect.value || 'newest', 1);
            }
        }

        toggleRole('adopter');
        btnAdopter.addEventListener('click', () => toggleRole('adopter'));
        btnRehome.addEventListener('click',  () => toggleRole('rehome'));
    });
</script>
@endpush
@endsection