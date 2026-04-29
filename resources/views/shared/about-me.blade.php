@extends('shared.view_main')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>About Me</h1>
        </div>
    </div>

    <div class="board">
        <div class="board-title">
            <span>My Profile</span>
        </div>
        
        <form id="profile-form" method="POST" action="{{ auth()->user()->role === 'admin' ? route('admin.profile.update') : route('petlover.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="prof-info-detail">
                {{-- LEFT COLUMN --}}
                <div class="profile-image-section">
                    <div class="circular-preview">
                        <img id="profile-preview-img" 
                             src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/profile.png') }}" 
                             alt="Profile">
                    </div>
                    
                    <input type="file" name="profile_image" id="profile_image_input" accept="image/*" style="display: none;">
                    
                    <button type="button" id="edit-toggle-btn" class="profile-action-btn" onclick="toggleEditMode()">
                        Edit Profile
                    </button>

                    <button type="button" class="profile-action-btn delete-acc-btn" onclick="confirmDelete()">
                        Delete Account
                    </button>
                </div>

                {{-- RIGHT COLUMN: Grid Format --}}
                <div class="prof-info-grid">
                    <div class="form-group">
                        <label>First Name:</label>
                        <input type="text" name="first_name" class="custom-input profile-field" value="{{ $user->first_name ?? '' }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Last Name:</label>
                        <input type="text" name="last_name" class="custom-input profile-field" value="{{ $user->last_name ?? '' }}" readonly>
                    </div>

                    {{-- Gender: input in view mode, select in edit mode --}}
                    <div class="form-group" id="gender-group">
                        <label>Gender:</label>
                        {{-- Read-only display (view mode) --}}
                        <input type="text" id="gender-display" class="custom-input" value="{{ $user->gender ?? '' }}" readonly style="display:block;">
                        {{-- Editable select (edit mode) --}}
                        <select name="gender" id="gender-select" class="custom-input profile-field" style="display:none;">
                            <option value="">-- Select --</option>
                            <option value="Male"   {{ ($user->gender ?? '') === 'Male'   ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ ($user->gender ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other"  {{ ($user->gender ?? '') === 'Other'  ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Birthday:</label>
                        <input type="date" name="birthdate" class="custom-input profile-field" value="{{ $user->birthdate ?? '' }}" readonly>
                    </div>

                    {{-- Marital Status: input in view mode, select in edit mode --}}
                    <div class="form-group" id="status-group">
                        <label>Status:</label>
                        {{-- Read-only display (view mode) --}}
                        <input type="text" id="status-display" class="custom-input" value="{{ $user->marital_status ?? '' }}" readonly style="display:block;">
                        {{-- Editable select (edit mode) --}}
                        <select name="marital_status" id="status-select" class="custom-input profile-field" style="display:none;">
                            <option value="">-- Select --</option>
                            <option value="Single"   {{ ($user->marital_status ?? '') === 'Single'   ? 'selected' : '' }}>Single</option>
                            <option value="Married"  {{ ($user->marital_status ?? '') === 'Married'  ? 'selected' : '' }}>Married</option>
                            <option value="Divorced" {{ ($user->marital_status ?? '') === 'Divorced' ? 'selected' : '' }}>Divorced</option>
                            <option value="Widowed"  {{ ($user->marital_status ?? '') === 'Widowed'  ? 'selected' : '' }}>Widowed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Age:</label>
                        <input type="text" class="custom-input" value="{{ $user->age ?? '' }}" readonly>
                    </div>

                    <div class="form-group full-width">
                        <label>Email:</label>
                        <input type="email" name="email" class="custom-input profile-field" value="{{ $user->email ?? '' }}" readonly>
                    </div>

                    <div class="form-group full-width">
                        <label>Contact:</label>
                        <input type="text" name="contact_number" class="custom-input profile-field" value="{{ $user->contact_number ?? '' }}" readonly>
                    </div>

                    <div class="form-group full-width">
                        <label>Address:</label>
                        <textarea name="address" class="custom-input profile-field" rows="2" readonly>{{ $user->address ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <button type="submit" id="save-btn" class="post-btn" style="display: none;">
                Save Changes
            </button>
        </form>
    </div>

    <br>

    @auth
        @if(auth()->user()->role === 'pet_lover')
            <div id="adopting-list" class="board">
                <div class="board-title">
                    <span>My Pets for Adoption</span>
                </div>
                <div class="row" style="margin-top: 40px; padding: 20px;">
                    <div class="pet_cards col-md-12" style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center;">
                        @forelse($myPets as $pet)
                            @include('others.pet-card', ['pet' => $pet]) 
                        @empty
                            <p>No pets posted yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    @endauth
</div>

{{-- Hidden Logout form to handle Delete --}}
<form id="delete-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
    let isEditing = false;

    function toggleEditMode() {
        const btn        = document.getElementById('edit-toggle-btn');
        const saveBtn    = document.getElementById('save-btn');
        const fields     = document.querySelectorAll('.profile-field');
        const fileInput  = document.getElementById('profile_image_input');

        // Gender & marital status swap elements
        const genderDisplay  = document.getElementById('gender-display');
        const genderSelect   = document.getElementById('gender-select');
        const statusDisplay  = document.getElementById('status-display');
        const statusSelect   = document.getElementById('status-select');

        if (!isEditing) {
            // Enter edit mode
            isEditing = true;

            // Enable text inputs / textareas (but NOT the display-only ones)
            fields.forEach(function(field) {
                if (field.tagName === 'SELECT') return; // handled separately
                field.removeAttribute('readonly');
            });

            // Swap gender to select
            genderDisplay.style.display = 'none';
            genderSelect.style.display  = 'block';

            // Swap marital status to select
            statusDisplay.style.display = 'none';
            statusSelect.style.display  = 'block';

            saveBtn.style.display = 'block';
            btn.innerText = 'Cancel';

            // Allow clicking image to change
            const preview = document.getElementById('profile-preview-img');
            preview.style.cursor = 'pointer';
            preview.onclick = () => fileInput.click();

        } else {
            // Cancel: reload to reset everything
            location.reload();
        }
    }

    function confirmDelete() {
        if (confirm("Are you sure you want to delete your account?")) {
            document.getElementById('delete-form').submit();
        }
    }

    document.getElementById('profile_image_input').onchange = function(evt) {
        const [file] = this.files;
        if (file) {
            document.getElementById('profile-preview-img').src = URL.createObjectURL(file);
        }
    };
</script>
@endsection