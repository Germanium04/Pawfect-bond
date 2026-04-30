@php $isAdmin = $isAdmin ?? (auth()->user()->role === 'admin'); @endphp

<div class="pet-info-pop">

    <span class="close-btn" onclick="{{ $isAdmin ? 'closeAdminPetInfo()' : 'closePetInfo()' }}">×</span>

    <div class="pet-about">

        <h2 style="text-align:center; margin:10px auto 0 auto; font-size:24px;">PET INFORMATION</h2>

        <div class="pp-row1">
            <div class="pet-pic-pp">
                <img id="{{ $isAdmin ? 'admin-popup-image' : 'popup-image' }}"
                     src="" alt="pet"
                     style="width:100%; height:100%; object-fit:cover; border-radius:10px;">
            </div>
            <div class="pet-name-pp">
                <h2 id="{{ $isAdmin ? 'admin-popup-name' : 'popup-name' }}"></h2>
                <hr class="pp-name">
                <label>Pet Name</label>
            </div>
        </div>

        <div class="pop-top">
            <div class="pet-mini-inf">
                <label>Age:</label>
                <input id="{{ $isAdmin ? 'admin-popup-age' : 'popup-age' }}" class="pop" readonly>
            </div>
            <div class="pet-mini-inf">
                <label>Gender:</label>
                <input id="{{ $isAdmin ? 'admin-popup-gender' : 'popup-gender' }}" class="pop" readonly>
            </div>
            <div class="pet-mini-inf">
                <label>Breed:</label>
                <input id="{{ $isAdmin ? 'admin-popup-breed' : 'popup-breed' }}" class="pop" readonly>
            </div>
            <div class="pet-mini-inf">
                <label>Address:</label>
                <input id="{{ $isAdmin ? 'admin-popup-address' : 'popup-address' }}" class="pop" readonly>
            </div>
        </div>

        <hr style="width:90%; margin:15px auto; border:2px solid black; border-radius:5px;">

        <div class="pop-top">
            <div class="pet-mini-inf">
                <label>Likes:</label>
                <input id="{{ $isAdmin ? 'admin-popup-like' : 'popup-like' }}" class="pop" readonly>
            </div>
            <div class="pet-mini-inf">
                <label>Owner:</label>
                <input id="{{ $isAdmin ? 'admin-popup-owner' : 'popup-owner' }}" class="pop" readonly>
            </div>
            <div class="pet-mini-inf">
                <label>Dislike:</label>
                <input id="{{ $isAdmin ? 'admin-popup-dislike' : 'popup-dislike' }}" class="pop" readonly>
            </div>
        </div>

        <div class="pop-lower">
            <label>Personality:</label>
            <input class="pop" id="{{ $isAdmin ? 'admin-popup-personality' : 'popup-personality' }}" readonly>
        </div>

        <div style="margin: 50px 25px 10px 25px;">
            <label>Medical History:</label>
            <table class="medical-table">
                <thead>
                    <tr>
                        <th>Medical Record</th>
                        <th>Taken</th>
                        <th>Latest Date</th>
                        <th>Certificate</th>
                    </tr>
                </thead>
                <tbody id="{{ $isAdmin ? 'admin-popup-medical-body' : 'popup-medical-body' }}"></tbody>
            </table>
        </div>

        {{-- ══ BOTTOM BUTTONS ══ --}}
        @if($isAdmin)
            <div class="pop-btns">
                <div class="right-pop-btn">
                    <form id="admin-popup-delete-form" method="POST" action=""
                          onsubmit="return confirm('Are you sure you want to delete this pet?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="report-p">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div id="report-modal" class="report-modal-overlay">
                <div class="report-modal-box">
                    <span class="close-btn" onclick="closeReportModal()">×</span>
                    <h3>Report this Pet Post</h3>
                    <form action="{{ route('report.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="report_type"      value="post">
                        <input type="hidden" name="reported_pet_id"  id="report-modal-pet-id"  value="">
                        <input type="hidden" name="reported_user_id" id="report-modal-user-id" value="">
                        <label>Reason for reporting:</label>
                        <textarea name="reason" required rows="4"
                                  class="report-modal-textarea"
                                  placeholder="Describe the issue…"></textarea>
                        <div class="report-modal-actions">
                            <button type="button" class="report-modal-cancel" onclick="closeReportModal()">Cancel</button>
                            <button type="submit" class="report-modal-submit">Submit Report</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="pop-btns">
                <div class="left-pop-btn">
                    <button type="button" class="report-p" onclick="openReportModal()">Report</button>
                </div>
                <div class="right-pop-btn">
                    <div class="btn-grp">
                        <button class="mess-p" id="popup-msg-btn" type="button">Message</button>
                    </div>
                    <div class="btn-group-2 happy">
                        <div class="paw2">
                            <img src="{{ asset('assets/paw2.png') }}">
                        </div>
                        <form action="{{ route('petlover.adoption-request') }}" method="POST">
                            @csrf
                            <input type="hidden" name="pet_id" id="popup-pet-id" value="">
                            <button type="submit" class="adopt-p">Adopt</button>
                        </form>
                    </div>
                </div>
            </div>

            <script>
            function openReportModal() {
                document.getElementById('report-modal-pet-id').value  = document.getElementById('popup-pet-id').value || '';
                document.getElementById('report-modal-user-id').value = window._popupOwnerId || '';
                document.getElementById('report-modal').classList.add('open');
            }
            function closeReportModal() {
                document.getElementById('report-modal').classList.remove('open');
            }
            document.getElementById('report-modal')?.addEventListener('click', function(e) {
                if (e.target === this) closeReportModal();
            });
            </script>
        @endif

    </div>
</div>