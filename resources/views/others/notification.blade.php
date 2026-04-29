@php
    $notifications = \App\Models\Notification::where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->take(15)
        ->get();

    $unreadCount = $notifications->where('is_read', false)->count();

    $icons = [
        'adoption_request'  => '🐾',
        'adoption_accepted' => '🎉',
        'adoption_declined' => '💔',
        'new_message'       => '💬',
        'reported'          => '🚨',
        'admin_action'      => '⚠️',
    ];
@endphp

<div class="arrow-try"></div>
<div class="notif-box">

    <div class="notif-header">
        <h3>Notifications</h3>
        @if($unreadCount > 0)
            <button class="notif-mark-all" onclick="markAllRead()">Mark all as read</button>
        @endif
    </div>

    <div class="notif-list">
        @forelse($notifications as $notif)
            <div class="notif-item {{ $notif->is_read ? 'read' : 'unread' }}">
                <span class="notif-icon">{{ $icons[$notif->type] ?? '🔔' }}</span>
                <div class="notif-content">
                    <p class="notif-title">{{ $notif->title }}</p>
                    <p class="notif-message">{{ $notif->message }}</p>
                    <span class="notif-time">{{ $notif->created_at->diffForHumans() }}</span>
                </div>
                @if(!$notif->is_read)
                    <span class="notif-dot"></span>
                @endif
            </div>
        @empty
            <div class="notif-empty">
                <span>🐶</span>
                <p>No notifications yet!</p>
            </div>
        @endforelse
    </div>

</div>

<script>
function markAllRead() {
    // Determine route based on role
    const role = '{{ auth()->user()->role }}';
    const url  = role === 'admin'
        ? '{{ route("admin.notifications.mark-read") }}'
        : '{{ route("petlover.notifications.mark-read") }}';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(() => {
        // Remove unread styling and dots
        document.querySelectorAll('.notif-item.unread').forEach(el => {
            el.classList.remove('unread');
            el.classList.add('read');
        });
        document.querySelectorAll('.notif-dot').forEach(el => el.remove());
        document.querySelector('.notif-mark-all')?.remove();

        // Clear the badge in the header
        const badge = document.querySelector('.badge');
        if (badge) badge.textContent = '';
    });
}
</script>