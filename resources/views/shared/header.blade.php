<div class="header">
    <div class="Title">
        <h1 style="font-size: 30px;">
            <img src="{{ asset('assets/paws.png') }}" style="width: 30px; height: 30px;">
            Paw-fect Bond
        </h1>
    </div>

    <div class="action">
        @auth
            <div class="notification-wrapper" style="position: relative; display: inline-block;">

                @php
                    $notifUnread = \App\Models\Notification::where('user_id', auth()->id())
                                    ->where('is_read', false)
                                    ->count();
                @endphp

                <button type="button" id="notif-button" class="btn-notification" style="background: none; border: none; padding: 0; position: relative;">
                    <img src="{{ asset('assets/notif.png') }}" style="width:45px; height:45px; object-fit:cover; border-radius:50%;">
                    @if($notifUnread > 0)
                        <span class="badge">{{ $notifUnread > 99 ? '99+' : $notifUnread }}</span>
                    @endif
                </button>

                <div id="notif-box" style="display: none; position: absolute; right: 0; top: 55px; z-index: 100;">
                    @include('others.notification')
                </div>

            </div>

            <a href="{{ auth()->user()->role === 'admin' ? route('admin.about-me') : route('petlover.about-me') }}" class="btn-profile">
                <img src="{{ auth()->user()->profile_image 
                        ? asset('storage/' . auth()->user()->profile_image) 
                        : asset('assets/profile.png') }}" 
                    style="width:45px; height:45px; object-fit:cover; border-radius:50%;">
            </a>
        @endauth
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.getElementById('notif-button');
        const box    = document.getElementById('notif-box');

        if (button && box) {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const isHidden = box.style.display === 'none';
                box.style.display = isHidden ? 'block' : 'none';
            });

            document.addEventListener('click', function(e) {
                if (!box.contains(e.target) && !button.contains(e.target)) {
                    box.style.display = 'none';
                }
            });
        }
    });
</script>