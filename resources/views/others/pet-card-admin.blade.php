{{-- Admin pet card: shows View + status badge instead of age/gender text --}}
<div class="pet-frame admin-pet-frame"
     data-name="{{ $pet->name }}"
     data-age="{{ $pet->age }}"
     data-gender="{{ $pet->gender }}"
     data-image="{{ asset('storage/' . $pet->pet_image) }}"
     data-owner="{{ ($pet->owner->first_name ?? 'N/A') . ' ' . ($pet->owner->last_name ?? '') }}"
     data-owner-id="{{ $pet->owner_id }}"
     data-breed="{{ $pet->breed }}"
     data-address="{{ $pet->owner->address ?? 'N/A' }}"
     data-likes="{{ $pet->likes }}"
     data-dislikes="{{ $pet->dislikes }}"
     data-personality="{{ $pet->personality }}"
     data-pet-id="{{ $pet->pet_id }}"
     data-medical="{{ json_encode(
         $pet->medicalRecord ? [
             ['type'=>'Vaccination', 'taken'=>$pet->medicalRecord->vaccinated,  'date'=>$pet->medicalRecord->vaccinated_date,  'cert'=> $pet->medicalRecord->vaccinated_certificate  ? asset('storage/'.$pet->medicalRecord->vaccinated_certificate)  : null],
             ['type'=>'Deworming',   'taken'=>$pet->medicalRecord->dewormed,    'date'=>$pet->medicalRecord->dewormed_date,    'cert'=> $pet->medicalRecord->dewormed_certificate    ? asset('storage/'.$pet->medicalRecord->dewormed_certificate)    : null],
             ['type'=>'Neutering',   'taken'=>$pet->medicalRecord->neutered,    'date'=>$pet->medicalRecord->neutered_date,    'cert'=> $pet->medicalRecord->neutered_certificate    ? asset('storage/'.$pet->medicalRecord->neutered_certificate)    : null],
         ] : []
     ) }}"
     style="cursor:default; width:200px;">

    <div class="frame-badge">
        <img src="{{ asset('assets/paw.png') }}" alt="paw icon">
    </div>

    <div class="pet-picture">
        @if(str_starts_with($pet->pet_image, 'assets/'))
            <img src="{{ asset($pet->pet_image) }}" alt="{{ $pet->name }}">
        @else
            <img src="{{ asset('storage/' . $pet->pet_image) }}" alt="{{ $pet->name }}">
        @endif
    </div>

    <div class="pet-name">
        <h1>{{ $pet->name }}</h1>
    </div>

    {{-- Bottom area: two buttons like Image 1 --}}
    <div class="pet-mini-info" style="display:flex; flex-direction:row; justify-content:center; gap:10px; padding:30px 10px 15px 10px;">
        <button type="button"
                class="admin-pet-view-btn"
                onclick="showAdminPetInfo(this.closest('.pet-frame'))"
                style="background:#FFC570; border:none; border-radius:20px; padding:6px 18px; font-size:13px; cursor:pointer; color:#2E2E2E;">
            View
        </button>
        <span class="admin-pet-status-badge"
              style="border-radius:20px; padding:6px 14px; font-size:13px; white-space:nowrap;
                     background:{{ $pet->status === 'available' ? '#d4edda' : '#f8d7da' }};
                     color:{{ $pet->status === 'available' ? '#155724' : '#721c24' }}; border:none;">
            {{ ucfirst($pet->status) }}
        </span>
    </div>
</div>