{{-- Filter overlay --}}
<div class="filter-overlay" id="filter-overlay" onclick="toggleFilter()" style="display:none;"></div>

{{-- Filter dialog (centered, same pattern as pet-info-popup) --}}
<div id="filter-container" class="pet-popup" style="display:none;">
    <div class="filter-dialog">
        <span class="close-btn" onclick="toggleFilter()">×</span>

        <h2 style="text-align:center; margin:10px auto 16px auto; font-size:24px;">FILTERS</h2>

        <form method="GET" action="{{ request()->url() }}">
            <div class="filter-group">
                <label for="age">Age:</label>
                <input type="text" id="age" name="age" class="custom-input" value="{{ request('age') }}">
            </div>

            <div class="filter-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" class="custom-input">
                    <option value="">Any</option>
                    <option value="Male"   {{ request('gender') == 'Male'   ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ request('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Medical Records:</label>
                <div class="filter-checkbox-group">
                    @foreach(['Vaccination', 'Deworming', 'Neutering'] as $record)
                        <label class="filter-checkbox-label">
                            <input type="checkbox" name="medical_records[]" value="{{ $record }}"
                                {{ is_array(request('medical_records')) && in_array($record, request('medical_records')) ? 'checked' : '' }}>
                            {{ $record }}
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="filter-apply-btn">Apply Filters</button>
        </form>
    </div>
</div>

<script>
    function toggleFilter() {
        const popup   = document.getElementById('filter-container');
        const overlay = document.getElementById('filter-overlay');
        const isOpen  = popup.style.display === 'flex';
        popup.style.display   = isOpen ? 'none' : 'flex';
        overlay.style.display = isOpen ? 'none' : 'block';
    }
</script>