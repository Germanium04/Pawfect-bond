<div class="pet-info-pop">

    <span class="close-btn" onclick="closePetInfo()">×</span>

    <div class="pet-about">

        <h2 style="text-align:center; margin:10px auto 0 auto; font-size:24px;">PET INFORMATION</h2>

        <div class="pp-row1">
            <div class="pet-pic-pp">
                <img id="popup-image">
            </div>

            <div class="pet-name-pp">
                <h2 id="popup-name"></h2>
                <hr class="pp-name">
                <label for="name">Pet Name</label>
            </div>
        </div>

        <div class="pop-top">
            <div class="pet-mini-inf">
                <label for="age">Age:</label>
                <input id="popup-age" class="pop" value="" readonly>
            </div>
            <div class="pet-mini-inf">
                <label for="gender">Gender:</label>
                <input id="popup-gender" class="pop" value="" readonly>
            </div>
            <div class="pet-mini-inf">
                <label for="breed">Breed:</label>
                <input id="popup-breed" class="pop" value="" readonly>
            </div>
            <div class="pet-mini-inf">
                <label for="address">Address:</label>
                <input id="popup-address" class="pop" value="" readonly>
            </div>
        </div>

        <hr style="width: 90%; margin: 15px auto; border: 2px solid black; border-radius:5px;">

        <div class="pop-top">
            <div class="pet-mini-inf">
                <label for="like">Likes:</label>
                <input id="popup-like" class="pop" value="" readonly>
            </div>
            <div class="pet-mini-inf">
                <label for="owner">Owner:</label>
                <input id="popup-owner" class="pop" value="" readonly>
            </div>
            <div class="pet-mini-inf">
                <label for="dislike">Dislike:</label>
                <input id="popup-dislike" class="pop" value="" readonly>
            </div>
        </div>

        <div class="pop-lower">
            <label for="Personality">Personality:</label>
            <input class="pop" id="popup-personality" readonly></input>
        </div>

        {{-- ══ MEDICAL HISTORY TABLE ══ --}}
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
                <tbody id="popup-medical-body">
                    {{-- Rows injected by JS --}}
                </tbody>
            </table>
        </div>

        {{-- ══ REPORT MODAL ══ --}}
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

    </div>

</div>

<script>
function openReportModal() {
    // Pull the pet_id and owner_id that were set when the popup opened
    document.getElementById('report-modal-pet-id').value  = document.getElementById('popup-pet-id').value  || '';
    document.getElementById('report-modal-user-id').value = document.querySelector('#popup-msg-btn') ? (window._popupOwnerId || '') : '';
    document.getElementById('report-modal').classList.add('open');
}
function closeReportModal() {
    document.getElementById('report-modal').classList.remove('open');
}
// Close on backdrop click
document.getElementById('report-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeReportModal();
});
</script>