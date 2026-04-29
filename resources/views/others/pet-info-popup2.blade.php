<div class="pet-info-pop2">

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

        <div class="pop-top">
            <div class="pet-mini-inf">
                <label for="like">Likes:</label>
                <input id="popup-like" class="pop" value="" readonly>
            </div>
            <div class="pet-mini-inf">
                <label for="dislike">Dislike:</label>
                <input id="popup-dislike" class="pop" value="" readonly>
            </div>
        </div>

        <div class="pop-lower2">
            <label for="Personality">Personality:</label>
            <input class="pop" id="popup-personality" readonly>
        </div>

        {{-- ══ MEDICAL HISTORY TABLE ══ --}}
        <div style="margin: 10px 25px 10px 25px;">
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

        <br>

        <div class="lower-pop">
            <div class="adopt-count">
                <h1 class="count" id="popup-request-count">0</h1>
                <p>Adoption Requests</p>
            </div>
            <div class="pop-btns2">
                <div>
                    <button class="mess-p" id="popup-edit-btn" type="button">Edit</button>
                </div>
                <div>
                    <button type="button" id="popup-delete-btn" class="mess-p">Delete</button>
                </div>
                {{-- Only visible when the pet has been rehomed --}}
                <div id="popup-receipt-wrapper" style="display:none;">
                    <button type="button" id="popup-download-receipt-btn" class="mess-p"
                            style="background:#4CAF50; color:white; border:none;">
                        ⬇ Download Receipt
                    </button>
                </div>
            </div>
        </div>

    </div>

</div>