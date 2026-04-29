@forelse($requests as $request)
    @php
        $status = strtolower($request->status);
    @endphp

        <div class="minimodal" style="position:relative;"
             data-status="{{ $status }}"
             data-date="{{ $request->created_at->toISOString() }}">

            @if(isset($role) && ($role == 'Adopter' || $role == 'Rehome'))
                <img src="{{ asset('storage/' . $request->pet->pet_image) }}"
                    alt="{{ $request->pet->name }}"
                    style="width:100px; height:85px; border-radius:10px; object-fit:cover; flex-shrink:0;">

            @elseif(!isset($role))
                @if($request->report_type == 'post' && $request->reportedPet)
                    <img src="{{ asset('storage/' . $request->reportedPet->pet_image) }}"
                        alt="{{ $request->reportedPet->name }}"
                        style="width:100px; height:85px; border-radius:10px; object-fit:cover; flex-shrink:0;">
                @elseif($request->reportedUser && $request->reportedUser->profile_image)
                    <img src="{{ asset('storage/' . $request->reportedUser->profile_image) }}"
                        alt="{{ $request->reportedUser->first_name }}"
                        style="width:100px; height:85px; border-radius:10px; object-fit:cover; flex-shrink:0;">
                @else
                    <img src="{{ asset('assets/profile.png') }}" alt="No image"
                        style="width:100px; height:85px; border-radius:10px; object-fit:cover; flex-shrink:0;">
                @endif
            @endif

            <div class="minimodal_content">

                @if(isset($role) && ($role == 'Adopter' || $role == 'Rehome'))
                    @php $requestCount = $request->pet->adoptionRequests->count(); @endphp

                    <h2 style="color:#E68A2E; text-align:left; font-size:25px; margin:0;">
                        {{ $request->pet->name }}
                    </h2>
                    <p style="color:#FFF1DC; margin:0; font-size:15px;">
                        {{ $request->pet->age }}
                        @if($role == 'Rehome') — {{ $request->pet->gender }} @endif
                    </p>
                    <p style="color:#FFC570; margin:0; font-size:15px;">
                        {{ $requestCount }} {{ Str::plural('adoption request', $requestCount) }}
                    </p>
                    @if($role == 'Adopter' && $status == 'rejected')
                        <p style="color:#FFC570; margin:0; font-size:13px;">
                            {{ $request->updated_at->format('F d, Y') }}
                        </p>
                    @endif

                @else
                    <h2 style="color:#E68A2E; text-align:left; font-size:20px; margin:0;">
                        @if($request->report_type == 'post' && $request->reportedPet)
                            {{ $request->reportedPet->name }}
                        @elseif($request->reportedUser)
                            {{ $request->reportedUser->first_name }} {{ $request->reportedUser->last_name }}
                        @else
                            Unknown
                        @endif
                    </h2>
                    <p style="color:#FFF1DC; margin:0; font-size:14px;">
                        {{ $request->reason }}
                    </p>
                    <span class="minibadge" style="font-size:12px; padding:3px 10px;">
                        {{ ucfirst($request->report_type) }}
                    </span>
                @endif
            </div>

            <div style="display:flex; align-items:center; gap:10px; margin-left:auto; flex-shrink:0;">

                @if(isset($role) && $role == 'Adopter')
                    @if($status == 'pending')
                        <span class="minibadge"
                            style="background:transparent; border:2px solid #E68A2E; color:#E68A2E;">
                            Pending
                        </span>

                    @elseif($status == 'accepted')
                        <span class="minibadge" 
                            style="background:transparent; border:2px solid #62b365; color:#62b365;">
                            Accepted
                        </span>

                    @elseif($status == 'rejected')
                        <span class="minibadge"
                            style="background:transparent; border:2px solid #FB7070; color:#FB7070;">
                            Rejected
                        </span>
                    @endif

                @elseif(isset($role) && $role == 'Rehome')
                    @if($status == 'pending')
                        <form method="POST"
                            action="{{ route('petlover.adoption-accept', $request->request_id) }}"
                            onsubmit="return confirm('Accept this adoption request?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    style="background:#4CAF50; color:white; border:none; border-radius:20px; padding:8px 24px; cursor:pointer; font-size:14px;">
                                Accept
                            </button>
                        </form>
                        <form method="POST"
                            action="{{ route('petlover.adoption-decline', $request->request_id) }}"
                            onsubmit="return confirm('Decline this request?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    style="background:#FB7070; color:white; border:none; border-radius:20px; padding:8px 24px; cursor:pointer; font-size:14px;">
                                Decline
                            </button>
                        </form>

                    @elseif($status == 'accepted')
                        <span class="minibadge" 
                            style="background:transparent; border:2px solid #62b365; color:#62b365;">
                            Accepted
                        </span>

                    @elseif($status == 'rejected')
                        <span class="minibadge"
                            style="background:transparent; border:2px solid #FB7070; color:#FB7070;">
                            Declined
                        </span>
                    @endif

                @else
                    @if($status == 'pending')
                        <form method="POST" action="{{ route('admin.report-action', $request->report_id) }}"
                            onsubmit="return confirm('Warn this user?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="warned">
                            <button type="submit"
                                    style="background:#FFC570; color:#2E2E2E; border:none; border-radius:20px; padding:6px 16px; cursor:pointer; font-size:13px;">
                                Warn
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.report-action', $request->report_id) }}"
                            onsubmit="return confirm('Suspend this user?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="suspended">
                            <button type="submit"
                                    style="background:#FFFACD; color:#2E2E2E; border:none; border-radius:20px; padding:6px 16px; cursor:pointer; font-size:13px;">
                                Suspend
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.report-action', $request->report_id) }}"
                            onsubmit="return confirm('Ban this user?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="banned">
                            <button type="submit"
                                    style="background:#FB7070; color:white; border:none; border-radius:20px; padding:6px 16px; cursor:pointer; font-size:13px;">
                                Ban
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.report-action', $request->report_id) }}"
                            onsubmit="return confirm('Dismiss this report?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="dismissed">
                            <button type="submit"
                                    style="background:#E68A2E; color:white; border:none; border-radius:20px; padding:6px 16px; cursor:pointer; font-size:13px;">
                                Dismiss
                            </button>
                        </form>

                    @elseif($status == 'suspended')
                        <span class="minibadge" style="background:#FFFACD; color:#2E2E2E; border:1px solid #ccc;">
                            Suspended
                        </span>
                    @elseif($status == 'banned')
                        <span class="minibadge" style="background:#FB7070; color:white;">
                            Banned
                        </span>
                    @elseif($status == 'warned')
                        <span class="minibadge" style="background:#FFC570; color:#2E2E2E;">
                            Warned
                        </span>
                    @elseif($status == 'dismissed')
                        <span class="minibadge" style="background:#ccc; color:#2E2E2E;">
                            Dismissed
                        </span>
                    @endif
                @endif

                @if(isset($role) && $role == 'Adopter' && $status == 'pending')
                    <form method="POST"
                        action="{{ route('petlover.adoption-cancel', $request->request_id) }}"
                        onsubmit="return confirm('Cancel this request?')"
                        class="minimodal-cancel-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="minimodal-cancel-btn" style="margin: 0 auto 100px auto;">&#x00D7;</button>
                    </form>
                @endif

            </div>
        </div>

@empty
    <p style="color:#a07050; padding:20px; text-align:center;">No requests found here.</p>
@endforelse