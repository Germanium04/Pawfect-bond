@extends('shared.view_main')

@section('content')
<div class="container">
    <h1>Rehoming Center</h1>
    <p>Welcome to the Rehoming Center! Here you can find pets that need a new home. Browse through our listings and give a loving family to a deserving animal.</p>

    <div class="stats_cards_section row">
        <div class="stats-card col-md-3">
            <h3>Pet Post</h3>
            <h2>{{ $petPosted ?? 0 }}</h2>
        </div>
        <div class="stats-card col-md-3">
            <h3>Rehomed Pets</h3>
            <h2>{{ $rehomedPets ?? 0 }}</h2>
        </div>
        <div class="stats-card col-md-3">
            <h3>Pending Adoption</h3>
            <h2>{{ $pendingRequests ?? 0 }}</h2>
        </div>
    </div>

    <div class="rehome-section">
        <div class="left-rehome-section">
            <div class="board">
                <form method="POST" action="{{ route('petlover.post-pet.post') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="board-title">
                        <span>Pet Information</span>
                    </div>

                    <br>

                    <div class="pet-info-detail">
                        <div class="top-pet-info">
                            <div class="add-pet-image">
                                <label for="pet-upload" style="cursor: pointer;">
                                    <img src="{{ asset('assets/add-pet-pic.png') }}" alt="Add Pet" id="image-preview">
                                </label>
                                <input type="file" id="pet-upload" name="pet_image" style="display: none;" onchange="previewImage(event)">
                            </div>

                            <div class="pet-info-col">
                                <div class="form-group">
                                    <label>Name:</label>
                                    <input type="text" name="name" class="custom-input" required>
                                </div>
                                <div class="form-group">
                                    <label>Breed:</label>
                                    <input type="text" name="breed" class="custom-input" required>
                                </div>
                                <div class="form-group">
                                    <label>Gender:</label>
                                    <select name="gender" class="custom-input">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Birthday:</label>
                                    <input type="date" name="birthday" class="custom-input" required>
                                </div>
                            </div>
                        </div>

                        <div class="pet-attri">
                            <div class="form-group">
                                <label>Likes:</label>
                                <input type="text" name="likes" class="custom-input" required>
                            </div>
                            <div class="form-group">
                                <label>Dislikes:</label>
                                <input type="text" name="dislikes" class="custom-input" required>
                            </div>
                            <div class="form-group full-width">
                                <label>Personality:</label>
                                <input type="text" name="personality" class="custom-input" required>
                            </div>
                        </div>

                        {{-- ══ MEDICAL HISTORY ══ --}}
                        <div>
                            <label class="med-section-label">Medical History:</label>
                            <div class="medical-history">

                                {{-- ── VACCINATION ── --}}
                                <div class="med-row">
                                    <div class="med-label">Vaccination</div>
                                    <div class="med-toggle">
                                        <button type="button" class="toggle-btn active" data-target="vaccinated" data-value="0">No</button>
                                        <button type="button" class="toggle-btn"        data-target="vaccinated" data-value="1">Yes</button>
                                        <input type="hidden" name="vaccinated" id="vaccinated-val" value="0">
                                    </div>
                                    <div class="med-extras" id="vaccinated-extras">
                                        <input type="date" name="vaccinated_date" class="custom-input med-date">
                                        <label class="upload-btn">
                                            📎 Upload Certificate
                                            <input type="file" name="vaccinated_certificate" accept=".jpg,.jpeg,.png,.pdf" class="med-file-input">
                                        </label>
                                        <span class="file-name-display" id="vaccinated-filename">No file chosen</span>
                                    </div>
                                </div>

                                {{-- ── DEWORMING ── --}}
                                <div class="med-row">
                                    <div class="med-label">Deworming</div>
                                    <div class="med-toggle">
                                        <button type="button" class="toggle-btn active" data-target="dewormed" data-value="0">No</button>
                                        <button type="button" class="toggle-btn"        data-target="dewormed" data-value="1">Yes</button>
                                        <input type="hidden" name="dewormed" id="dewormed-val" value="0">
                                    </div>
                                    <div class="med-extras" id="dewormed-extras">
                                        <input type="date" name="dewormed_date" class="custom-input med-date">
                                        <label class="upload-btn">
                                            📎 Upload Certificate
                                            <input type="file" name="dewormed_certificate" accept=".jpg,.jpeg,.png,.pdf" class="med-file-input">
                                        </label>
                                        <span class="file-name-display" id="dewormed-filename">No file chosen</span>
                                    </div>
                                </div>

                                {{-- ── NEUTERING ── --}}
                                <div class="med-row">
                                    <div class="med-label">Spayed / Neutered</div>
                                    <div class="med-toggle">
                                        <button type="button" class="toggle-btn active" data-target="neutered" data-value="0">No</button>
                                        <button type="button" class="toggle-btn"        data-target="neutered" data-value="1">Yes</button>
                                        <input type="hidden" name="neutered" id="neutered-val" value="0">
                                    </div>
                                    <div class="med-extras" id="neutered-extras">
                                        <input type="date" name="neutered_date" class="custom-input med-date">
                                        <label class="upload-btn">
                                            📎 Upload Certificate
                                            <input type="file" name="neutered_certificate" accept=".jpg,.jpeg,.png,.pdf" class="med-file-input">
                                        </label>
                                        <span class="file-name-display" id="neutered-filename">No file chosen</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                        {{-- ══ END MEDICAL HISTORY ══ --}}

                    </div>
                    <br>
                    <button type="submit" class="post-btn">Post</button>
                </form>
            </div>
        </div>

        <div class="right-rehome-section">
            <h2>Posted Pets</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Pet Name</th>
                        <th>Date Posted</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pets as $pet)
                        @php
                            $age = \Carbon\Carbon::parse($pet->birthday)->age;
                            $med = $pet->medicalRecord;
                            $adoption = null;
                            if ($pet->status === 'rehomed') {
                                $adoption = \App\Models\Adoption::with('adopter')->where('pet_id', $pet->pet_id)->latest()->first();
                            }
                            $requestCount = $pet->adoptionRequests->count();
                        @endphp
                        <tr>
                            <td>{{ $pet->name }}</td>
                            <td>{{ $pet->created_at->format('Y-m-d') }}</td>
                            <td>
                                <span style="
                                    padding: 3px 10px;
                                    border-radius: 20px;
                                    font-size: 13px;
                                    font-weight: bold;
                                    background: {{ $pet->status === 'rehomed' ? '#4CAF50' : '#E68A2E' }};
                                    color: white;">
                                    {{ ucfirst($pet->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="#" class="view-pet-link"
                                    data-id="{{ $pet->pet_id }}"
                                    data-name="{{ $pet->name }}"
                                    data-age="{{ $age }} yr{{ $age != 1 ? 's' : '' }}"
                                    data-gender="{{ $pet->gender }}"
                                    data-breed="{{ $pet->breed }}"
                                    data-address="{{ $pet->owner->address ?? 'N/A' }}"
                                    data-likes="{{ $pet->likes }}"
                                    data-dislikes="{{ $pet->dislikes }}"
                                    data-personality="{{ $pet->personality }}"
                                    data-owner-id="{{ $pet->owner_id }}"
                                    data-owner-name="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}"
                                    data-status="{{ $pet->status }}"
                                    data-request-count="{{ $requestCount }}"
                                    data-image="{{ $pet->pet_image ? asset('storage/' . $pet->pet_image) : asset('assets/cutiepic.png') }}"
                                    data-vaccinated="{{ $med?->vaccinated ? 'Yes' : 'No' }}"
                                    data-vaccinated-date="{{ $med?->vaccinated_date ?? '' }}"
                                    data-vaccinated-cert="{{ $med?->vaccinated_certificate ? Storage::url($med->vaccinated_certificate) : '' }}"
                                    data-dewormed="{{ $med?->dewormed ? 'Yes' : 'No' }}"
                                    data-dewormed-date="{{ $med?->dewormed_date ?? '' }}"
                                    data-dewormed-cert="{{ $med?->dewormed_certificate ? Storage::url($med->dewormed_certificate) : '' }}"
                                    data-neutered="{{ $med?->neutered ? 'Yes' : 'No' }}"
                                    data-neutered-date="{{ $med?->neutered_date ?? '' }}"
                                    data-neutered-cert="{{ $med?->neutered_certificate ? Storage::url($med->neutered_certificate) : '' }}"
                                    {{-- Adoption / adopter data for receipt --}}
                                    data-adopter-name="{{ $adoption ? ($adoption->adopter->first_name . ' ' . $adoption->adopter->last_name) : '' }}"
                                    data-adopter-contact="{{ $adoption ? ($adoption->adopter->contact_number ?? 'N/A') : '' }}"
                                    data-adopter-address="{{ $adoption ? ($adoption->adopter->address ?? 'N/A') : '' }}"
                                    data-adoption-date="{{ $adoption ? $adoption->adoption_date : '' }}"
                                >View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Popup overlay (place OUTSIDE the <table>) --}}
            <div id="pet-popup-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:999;">
                <div class="pet-popup" style="position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:1000; overflow-y:auto; max-height:100vh;">
                    @include('others.pet-info-popup2')
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ HIDDEN PRINTABLE RECEIPT ══ --}}
{{-- Rendered off-screen; JS fills in details then window.print() targets it --}}
<div id="receipt-print-area" style="display:none;">
    <style>
        @media print {
            body > *:not(#receipt-print-area) { display: none !important; }
            #receipt-print-area { display: block !important; }
        }
    </style>
    <div id="receipt-content" style="font-family: Georgia, serif; padding: 60px; max-width: 700px; margin: auto; border: 3px double #333;">
        <div style="text-align:center; margin-bottom:30px;">
            <h1 style="font-size:28px; letter-spacing:2px; margin:0;">PAW-FECT BOND</h1>
            <p style="margin:4px 0; font-size:14px; color:#555;">Pet Adoption & Rehoming Platform</p>
            <h2 style="font-size:20px; margin-top:20px; border-bottom:2px solid #333; padding-bottom:8px;">
                Certificate of Pet Ownership Transfer
            </h2>
        </div>

        <p style="text-align:center; font-size:15px; line-height:1.8; margin-bottom:30px;">
            This certificate confirms the official transfer of ownership of the pet described below
            from the original owner to the new adopter, as processed through Paw-fect Bond.
        </p>

        <table style="width:100%; border-collapse:collapse; font-size:15px; margin-bottom:30px;">
            <tr style="background:#f5f0e8;">
                <td style="padding:10px 14px; font-weight:bold; width:40%; border:1px solid #ccc;">Pet Name</td>
                <td style="padding:10px 14px; border:1px solid #ccc;" id="r-pet-name"></td>
            </tr>
            <tr>
                <td style="padding:10px 14px; font-weight:bold; border:1px solid #ccc;">Breed</td>
                <td style="padding:10px 14px; border:1px solid #ccc;" id="r-breed"></td>
            </tr>
            <tr style="background:#f5f0e8;">
                <td style="padding:10px 14px; font-weight:bold; border:1px solid #ccc;">Age</td>
                <td style="padding:10px 14px; border:1px solid #ccc;" id="r-age"></td>
            </tr>
            <tr>
                <td style="padding:10px 14px; font-weight:bold; border:1px solid #ccc;">Gender</td>
                <td style="padding:10px 14px; border:1px solid #ccc;" id="r-gender"></td>
            </tr>
            <tr style="background:#f5f0e8;">
                <td style="padding:10px 14px; font-weight:bold; border:1px solid #ccc;">Previous Owner</td>
                <td style="padding:10px 14px; border:1px solid #ccc;" id="r-giver"></td>
            </tr>
            <tr>
                <td style="padding:10px 14px; font-weight:bold; border:1px solid #ccc;">New Owner (Adopter)</td>
                <td style="padding:10px 14px; border:1px solid #ccc;" id="r-adopter"></td>
            </tr>
            <tr style="background:#f5f0e8;">
                <td style="padding:10px 14px; font-weight:bold; border:1px solid #ccc;">Adopter Contact</td>
                <td style="padding:10px 14px; border:1px solid #ccc;" id="r-contact"></td>
            </tr>
            <tr>
                <td style="padding:10px 14px; font-weight:bold; border:1px solid #ccc;">Adopter Address</td>
                <td style="padding:10px 14px; border:1px solid #ccc;" id="r-address"></td>
            </tr>
            <tr style="background:#f5f0e8;">
                <td style="padding:10px 14px; font-weight:bold; border:1px solid #ccc;">Date of Transfer</td>
                <td style="padding:10px 14px; border:1px solid #ccc;" id="r-date"></td>
            </tr>
        </table>

        <div style="display:flex; justify-content:space-between; margin-top:60px;">
            <div style="text-align:center; width:40%;">
                <div style="border-top:2px solid #333; padding-top:8px;" id="r-giver-sig"></div>
                <p style="margin:4px 0; font-size:13px; color:#555;">Previous Owner Signature</p>
            </div>
            <div style="text-align:center; width:40%;">
                <div style="border-top:2px solid #333; padding-top:8px;" id="r-adopter-sig"></div>
                <p style="margin:4px 0; font-size:13px; color:#555;">New Owner Signature</p>
            </div>
        </div>

        <p style="text-align:center; font-size:12px; color:#999; margin-top:40px;">
            Generated by Paw-fect Bond &bull; <span id="r-generated-date"></span>
        </p>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // ── Image preview for post form ──
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                document.getElementById('image-preview').src = reader.result;
            };
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
        window.previewImage = previewImage;

        // ── Medical Yes/No toggles ──
        document.querySelectorAll('.toggle-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const target = this.dataset.target;
                const value  = this.dataset.value;
                const extras = document.getElementById(target + '-extras');
                const hidden = document.getElementById(target + '-val');

                hidden.value = value;

                this.closest('.med-toggle')
                    .querySelectorAll('.toggle-btn')
                    .forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                if (value === '1') {
                    extras.classList.add('open');
                } else {
                    extras.classList.remove('open');
                    extras.querySelector('input[type="date"]').value = '';
                    extras.querySelector('input[type="file"]').value = '';
                    extras.querySelector('.file-name-display').textContent = 'No file chosen';
                }
            });
        });

        // ── Show filename after upload ──
        document.querySelectorAll('.med-file-input').forEach(function (input) {
            input.addEventListener('change', function () {
                const display = this.closest('.med-extras').querySelector('.file-name-display');
                display.textContent = this.files.length > 0 ? this.files[0].name : 'No file chosen';
            });
        });

        // ── View pet popup ──
        const overlay = document.getElementById('pet-popup-overlay');

        document.querySelectorAll('.view-pet-link').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const d = this.dataset;

                document.getElementById('popup-image').src          = d.image;
                document.getElementById('popup-name').textContent   = d.name;
                document.getElementById('popup-age').value          = d.age;
                document.getElementById('popup-gender').value       = d.gender;
                document.getElementById('popup-breed').value        = d.breed;
                document.getElementById('popup-address').value      = d.address;
                document.getElementById('popup-like').value         = d.likes;
                document.getElementById('popup-dislike').value      = d.dislikes;
                document.getElementById('popup-personality').value  = d.personality;

                // Adoption request count
                document.getElementById('popup-request-count').textContent = d.requestCount || '0';

                // Show/hide Download Receipt button
                const receiptWrapper = document.getElementById('popup-receipt-wrapper');
                receiptWrapper.style.display = (d.status === 'rehomed') ? 'block' : 'none';

                // Wire Download Receipt button
                document.getElementById('popup-download-receipt-btn').onclick = function () {
                    printReceipt(d);
                };

                // Medical table
                const tbody = document.getElementById('popup-medical-body');
                tbody.innerHTML = '';
                const records = [
                    { label: 'Vaccination',     taken: d.vaccinated, date: d.vaccinatedDate, cert: d.vaccinatedCert },
                    { label: 'Deworming',       taken: d.dewormed,   date: d.dewormedDate,   cert: d.dewormedCert   },
                    { label: 'Spayed/Neutered', taken: d.neutered,   date: d.neuteredDate,   cert: d.neuteredCert   },
                ];
                records.forEach(function (r) {
                    const color   = r.taken === 'Yes' ? '#4CAF50' : '#FB7070';
                    const certHtml = r.cert ? `<a href="${r.cert}" target="_blank" style="color:#E68A2E;">View</a>` : '—';
                    tbody.innerHTML += `
                        <tr>
                            <td>${r.label}</td>
                            <td style="color:${color}; font-weight:bold;">${r.taken}</td>
                            <td>${r.date || '—'}</td>
                            <td>${certHtml}</td>
                        </tr>`;
                });

                // Edit / Delete placeholders
                document.getElementById('popup-edit-btn').onclick = function () {
                    alert('Edit coming soon!');
                };
                document.getElementById('popup-delete-btn').onclick = function () {
                    alert('Delete coming soon!');
                };

                overlay.style.display = 'block';
            });
        });

        // ── Close popup ──
        window.closePetInfo = function () {
            overlay.style.display = 'none';
        };

        overlay.addEventListener('click', function (e) {
            if (e.target === this) closePetInfo();
        });

        // ── Print / Download Receipt ──
        function printReceipt(d) {
            document.getElementById('r-pet-name').textContent   = d.name       || '—';
            document.getElementById('r-breed').textContent      = d.breed      || '—';
            document.getElementById('r-age').textContent        = d.age        || '—';
            document.getElementById('r-gender').textContent     = d.gender     || '—';
            document.getElementById('r-giver').textContent      = d.ownerName  || '—';
            document.getElementById('r-adopter').textContent    = d.adopterName    || '—';
            document.getElementById('r-contact').textContent    = d.adopterContact || '—';
            document.getElementById('r-address').textContent    = d.adopterAddress || '—';
            document.getElementById('r-giver-sig').textContent  = d.ownerName  || '—';
            document.getElementById('r-adopter-sig').textContent = d.adopterName || '—';

            // Format adoption date nicely
            const rawDate = d.adoptionDate;
            let formattedDate = '—';
            if (rawDate) {
                const dt = new Date(rawDate);
                formattedDate = dt.toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' });
            }
            document.getElementById('r-date').textContent = formattedDate;

            // Generated timestamp
            document.getElementById('r-generated-date').textContent =
                new Date().toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' });

            // Show the print area and trigger print
            const area = document.getElementById('receipt-print-area');
            area.style.display = 'block';
            window.print();
            area.style.display = 'none';
        }

    });
</script>
@endpush