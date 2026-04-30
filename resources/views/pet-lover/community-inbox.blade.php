@extends('shared.view_main')

@section('content')
<div class="container">
    <h1>Community Inbox</h1>
    <p>Welcome to the Community Inbox! Here you can communicate with other pet lovers.</p>

    <div class="stats_cards_section row">
        <div class="stats-card col-md-3">
            <h3>Request</h3>
            <h2>{{ $inquiryCount ?? 0 }}</h2>
        </div>
        <div class="stats-card col-md-3">
            <h3>Inquiries</h3>
            {{-- Use $totalConversations (all buckets combined) for the accurate count --}}
            <h2>{{ $totalConversations ?? $conversations->count() }}</h2>
        </div>
        <div class="stats-card col-md-3">
            <h3>Unread</h3>
            <h2>{{ $unreadCount ?? 0 }}</h2>
        </div>
    </div>

    <div class="inbox_messages">

        {{-- LEFT BAR --}}
        <div class="left-bar">
            <div class="tab-option">
                <button type="button" id="btn-active"   class="tab-btn active">
                    Active
                    @if($conversations->count() > 0)
                        <span class="tab-count">{{ $conversations->count() }}</span>
                    @endif
                </button>
                <button type="button" id="btn-archived" class="tab-btn">
                    Archived
                    @if($archivedConversations->count() > 0)
                        <span class="tab-count">{{ $archivedConversations->count() }}</span>
                    @endif
                </button>
                <button type="button" id="btn-blocked"  class="tab-btn">
                    Blocked
                    @if($blockedConversations->count() > 0)
                        <span class="tab-count">{{ $blockedConversations->count() }}</span>
                    @endif
                </button>
            </div>

            <div class="people-list">
                <ul id="list-active">
                    @forelse($conversations as $person)
                        <li data-user-id="{{ $person->user_id }}">
                            <span class="conv-avatar">{{ strtoupper(substr($person->first_name, 0, 1)) }}</span>
                            <span class="conv-name">{{ $person->first_name }} {{ $person->last_name }}</span>
                        </li>
                    @empty
                        <li class="no-convo">No conversations yet.</li>
                    @endforelse
                </ul>

                <ul id="list-archived" style="display:none;">
                    @forelse($archivedConversations as $person)
                        <li data-user-id="{{ $person->user_id }}">
                            <span class="conv-avatar">{{ strtoupper(substr($person->first_name, 0, 1)) }}</span>
                            <span class="conv-name">{{ $person->first_name }} {{ $person->last_name }}</span>
                        </li>
                    @empty
                        <li class="no-convo">No archived conversations.</li>
                    @endforelse
                </ul>

                <ul id="list-blocked" style="display:none;">
                    @forelse($blockedConversations as $person)
                        <li data-user-id="{{ $person->user_id }}">
                            <span class="conv-avatar">{{ strtoupper(substr($person->first_name, 0, 1)) }}</span>
                            <span class="conv-name">{{ $person->first_name }} {{ $person->last_name }}</span>
                        </li>
                    @empty
                        <li class="no-convo">No blocked users.</li>
                    @endforelse
                </ul>
            </div>
            {{-- .conv-pagination is injected here by JS --}}
        </div>

        {{-- RIGHT BAR --}}
        <div class="right-bar">
            <div class="action-buttons">
                <button type="button" id="btn-archive" class="top-action-btn">Archive</button>
                <button type="button" id="btn-block"   class="top-action-btn">Block</button>
                <button type="button" id="btn-report"  class="top-action-btn">Report</button>
            </div>

            {{-- Report Modal --}}
            <div id="inbox-report-modal" class="report-modal-overlay">
                <div class="report-modal-box">
                    <span class="close-btn" onclick="closeInboxReportModal()">×</span>
                    <h3>Report this User</h3>
                    <p id="inbox-report-target-name" class="report-modal-target"></p>
                    <form action="{{ route('report.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="report_type"      value="user">
                        <input type="hidden" name="reported_user_id" id="report-user-id" value="">
                        <label>Reason for reporting:</label>
                        <textarea name="reason" required rows="4"
                                  class="report-modal-textarea"
                                  placeholder="Describe the issue…"></textarea>
                        <div class="report-modal-actions">
                            <button type="button" class="report-modal-cancel" onclick="closeInboxReportModal()">Cancel</button>
                            <button type="submit" class="report-modal-submit">Submit Report</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="message-content empty-state" id="message-content"
                 style="height:400px; overflow-y:auto; overflow-x:hidden; flex-shrink:0;">
                Select a conversation to view messages.
            </div>

            <div class="reply-section">
                <button type="button" class="icon-btn" title="Pictures"
                        onclick="document.getElementById('image-upload').click()">
                    <img src="{{ asset('assets/send-pet-pic.png') }}" class="upload-pic" alt="Img">
                </button>
                <input type="file" id="image-upload" accept="image/*" style="display:none;">

                <textarea id="reply-input" placeholder="Write a message..."></textarea>

                <button type="button" class="btn-send-nav" id="btn-send">
                    <img src="{{ asset('assets/send.png') }}" alt="Send">
                </button>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const authId     = parseInt('{{ Auth::id() }}');
    const openUserId = parseInt('{{ $openUserId ?? 0 }}') || null;
    let activeUserId = null;

    /* ── CSRF ─────────────────────────────────────────────────── */
    function getCsrf() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) return meta.getAttribute('content');
        const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
        return match ? decodeURIComponent(match[1]) : '';
    }

    /* ── Tab lists map ────────────────────────────────────────── */
    const tabLists = {
        'btn-active':   document.querySelector('#list-active'),
        'btn-archived': document.querySelector('#list-archived'),
        'btn-blocked':  document.querySelector('#list-blocked')
    };

    /* ══════════════════════════════════════════════════════════
       CONVERSATION LIST PAGINATION
       Uses .conv-pagination class (styled in pagination.css)
       10 items per page per tab.
    ══════════════════════════════════════════════════════════ */
    const CONV_PER_PAGE = 10;
    const paginationState = { 'list-active': 1, 'list-archived': 1, 'list-blocked': 1 };

    function renderConvPagination(listId) {
        const ul = document.getElementById(listId);
        if (!ul) return;

        const allLi    = Array.from(ul.querySelectorAll('li[data-user-id]'));
        const total    = allLi.length;
        const totalPgs = Math.max(1, Math.ceil(total / CONV_PER_PAGE));
        const page     = paginationState[listId] || 1;

        /* show/hide items for current page */
        allLi.forEach(function (li, i) {
            const start = (page - 1) * CONV_PER_PAGE;
            li.style.display = (i >= start && i < start + CONV_PER_PAGE) ? '' : 'none';
        });

        /* remove old paginator */
        const old = ul.parentElement.querySelector('.conv-pagination');
        if (old) old.remove();

        /* no paginator needed for ≤1 page */
        if (totalPgs <= 1) return;

        /* build paginator */
        const pg = document.createElement('div');
        pg.className = 'conv-pagination';

        function makeBtn(label, toPage, disabled, active) {
            const b = document.createElement('button');
            b.textContent = label;
            b.disabled    = disabled;
            if (active)    b.classList.add('pg-active');
            if (disabled)  b.classList.add('pg-disabled');
            if (!disabled) b.addEventListener('click', function () {
                paginationState[listId] = toPage;
                renderConvPagination(listId);
            });
            return b;
        }

        /* «  ‹  1 2 3 …  ›  » */
        pg.appendChild(makeBtn('«', 1,          page === 1,          false));
        pg.appendChild(makeBtn('‹', page - 1,   page === 1,          false));

        /* sliding window: show max 5 page numbers */
        const windowSize = 5;
        let start = Math.max(1, page - Math.floor(windowSize / 2));
        let end   = Math.min(totalPgs, start + windowSize - 1);
        if (end - start < windowSize - 1) start = Math.max(1, end - windowSize + 1);

        if (start > 1) {
            pg.appendChild(makeBtn(1, 1, false, false));
            if (start > 2) {
                const dots = document.createElement('button');
                dots.textContent = '…';
                dots.disabled    = true;
                dots.classList.add('pg-disabled');
                pg.appendChild(dots);
            }
        }

        for (let i = start; i <= end; i++) {
            pg.appendChild(makeBtn(i, i, false, i === page));
        }

        if (end < totalPgs) {
            if (end < totalPgs - 1) {
                const dots = document.createElement('button');
                dots.textContent = '…';
                dots.disabled    = true;
                dots.classList.add('pg-disabled');
                pg.appendChild(dots);
            }
            pg.appendChild(makeBtn(totalPgs, totalPgs, false, false));
        }

        pg.appendChild(makeBtn('›', page + 1,   page === totalPgs,   false));
        pg.appendChild(makeBtn('»', totalPgs,    page === totalPgs,   false));

        ul.parentElement.appendChild(pg);
    }

    /* init pagination for all three lists */
    renderConvPagination('list-active');
    renderConvPagination('list-archived');
    renderConvPagination('list-blocked');

    /* ── Tab switching ────────────────────────────────────────── */
    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            Object.values(tabLists).forEach(l => { if (l) l.style.display = 'none'; });
            /* hide any existing paginator */
            document.querySelectorAll('.conv-pagination').forEach(p => p.style.display = 'none');

            this.classList.add('active');
            const target = tabLists[this.id];
            if (target) {
                target.style.display = 'block';
                /* re-render pagination for this tab */
                renderConvPagination(target.id);
            }
            activeUserId = null;
            resetMessageArea();
        });
    });

    /* ── Person click ─────────────────────────────────────────── */
    function bindPersonClicks() {
        document.querySelectorAll('.people-list li[data-user-id]').forEach(function (li) {
            li.addEventListener('click', function () {
                document.querySelectorAll('.people-list li').forEach(l => l.classList.remove('active-person'));
                this.classList.add('active-person');
                activeUserId = this.dataset.userId;
                loadMessages(activeUserId);
            });
        });
    }
    bindPersonClicks();

    /* ── Archive button ───────────────────────────────────────── */
    document.getElementById('btn-archive').addEventListener('click', function () {
        if (!activeUserId) { alert('Select a conversation first.'); return; }
        if (!confirm('Archive this conversation?')) return;

        fetch('/pet-lover/community-inbox/archive', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
            body: JSON.stringify({ user_id: parseInt(activeUserId) })
        }).then(function () {
            const li = document.querySelector('#list-active li[data-user-id="' + activeUserId + '"]');
            if (li) {
                const archivedList  = document.querySelector('#list-archived');
                const placeholder   = archivedList.querySelector('.no-convo');
                if (placeholder) placeholder.remove();
                li.style.display = '';          // reset hidden state from pagination
                archivedList.appendChild(li);
            }
            activeUserId = null;
            resetMessageArea();
            /* re-render both affected lists */
            paginationState['list-active']   = 1;
            paginationState['list-archived'] = 1;
            renderConvPagination('list-active');
        });
    });

    /* ── Block button ─────────────────────────────────────────── */
    document.getElementById('btn-block').addEventListener('click', function () {
        if (!activeUserId) { alert('Select a conversation first.'); return; }
        if (!confirm('Block this user? They will not be able to message you.')) return;

        fetch('/pet-lover/community-inbox/block', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
            body: JSON.stringify({ user_id: parseInt(activeUserId) })
        }).then(function () {
            const li = document.querySelector('#list-active li[data-user-id="' + activeUserId + '"]');
            if (li) {
                const blockedList  = document.querySelector('#list-blocked');
                const placeholder  = blockedList.querySelector('.no-convo');
                if (placeholder) placeholder.remove();
                li.style.display = '';          // reset hidden state from pagination
                blockedList.appendChild(li);
            }
            activeUserId = null;
            resetMessageArea();
            /* re-render both affected lists */
            paginationState['list-active']  = 1;
            paginationState['list-blocked'] = 1;
            renderConvPagination('list-active');
        });
    });

    /* ── Report button ────────────────────────────────────────── */
    document.getElementById('btn-report').addEventListener('click', function () {
        if (!activeUserId) { alert('Select a conversation first.'); return; }

        const activeLi = document.querySelector('li[data-user-id="' + activeUserId + '"]');
        const name     = activeLi
            ? (activeLi.querySelector('.conv-name')?.textContent.trim() || activeLi.textContent.trim())
            : 'this user';

        document.getElementById('report-user-id').value              = activeUserId;
        document.getElementById('inbox-report-target-name').textContent = name;
        document.getElementById('inbox-report-modal').classList.add('open');
    });

    document.getElementById('inbox-report-modal').addEventListener('click', function (e) {
        if (e.target === this) closeInboxReportModal();
    });

    /* ── Auto-load first / ?with= conversation ────────────────── */
    let targetLi = null;
    if (openUserId) {
        document.querySelectorAll('#list-active li[data-user-id]').forEach(function (el) {
            if (parseInt(el.dataset.userId) === openUserId) targetLi = el;
        });
    }
    if (!targetLi) targetLi = document.querySelector('#list-active li[data-user-id]');

    if (targetLi) {
        document.querySelectorAll('.people-list li').forEach(l => l.classList.remove('active-person'));
        targetLi.classList.add('active-person');
        activeUserId = targetLi.dataset.userId;
        loadMessages(activeUserId);
    }

    /* ── Load messages ────────────────────────────────────────── */
    function loadMessages(userId) {
        const area = document.getElementById('message-content');
        area.classList.remove('empty-state');
        area.innerHTML = '<p style="color:#a07050;text-align:center;padding:20px;">Loading…</p>';

        fetch('/pet-lover/community-inbox/read/' + userId, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': getCsrf() }
        });

        fetch('/pet-lover/community-inbox/messages/' + userId)
            .then(function (res) {
                if (!res.ok) throw new Error('Server error ' + res.status);
                return res.json();
            })
            .then(function (messages) {
                area.innerHTML = '';
                if (messages.length === 0) {
                    area.classList.add('empty-state');
                    area.textContent = 'No messages yet. Say hello!';
                    return;
                }
                messages.forEach(function (msg) { renderMessage(msg); });
                area.scrollTop = area.scrollHeight;
            })
            .catch(function (err) {
                area.innerHTML = '';
                area.classList.add('empty-state');
                area.textContent = 'Failed to load messages.';
                console.error('Load error:', err);
            });
    }

    /* ── Render message bubble ────────────────────────────────── */
    function renderMessage(msg) {
        const area  = document.getElementById('message-content');
        const isMine = parseInt(msg.sender_id) === authId;

        const bubble = document.createElement('div');
        bubble.classList.add('msg-bubble', isMine ? 'msg-mine' : 'msg-theirs');

        const date    = new Date(msg.created_at);
        const timeStr = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        let content = '';
        if (msg.message_text.startsWith('image::')) {
            const path = msg.message_text.replace('image::', '');
            content = '<img src="/storage/' + path + '" style="max-width:200px;border-radius:10px;">';
        } else {
            content = '<div class="bubble-text">' + escapeHtml(msg.message_text) + '</div>';
        }

        bubble.innerHTML = content + '<div class="bubble-time">' + timeStr + '</div>';
        area.appendChild(bubble);
    }

    /* ── Send text ────────────────────────────────────────────── */
    document.getElementById('btn-send').addEventListener('click', sendMessage);
    document.getElementById('reply-input').addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });

    function sendMessage() {
        const input = document.getElementById('reply-input');
        const text  = input.value.trim();
        if (!text)          { alert('Please type a message.'); return; }
        if (!activeUserId)  { alert('Please select a conversation first.'); return; }

        const area = document.getElementById('message-content');
        if (area.classList.contains('empty-state')) {
            area.classList.remove('empty-state');
            area.innerHTML = '';
        }

        fetch('/pet-lover/community-inbox/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': getCsrf()
            },
            body: JSON.stringify({ receiver_id: parseInt(activeUserId), message_text: text })
        })
        .then(function (res) {
            if (!res.ok) return res.json().then(function (e) { throw e; });
            return res.json();
        })
        .then(function (msg) {
            input.value = '';
            renderMessage(msg);
            area.scrollTop = area.scrollHeight;
        })
        .catch(function (err) {
            console.error('Send failed:', err);
            alert('Send failed: ' + JSON.stringify(err));
        });
    }

    /* ── Send image ───────────────────────────────────────────── */
    document.getElementById('image-upload').addEventListener('change', function () {
        const file = this.files[0];
        if (!file || !activeUserId) return;

        const formData = new FormData();
        formData.append('receiver_id', parseInt(activeUserId));
        formData.append('image', file);

        fetch('/pet-lover/community-inbox/send-image', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
            body: formData
        })
        .then(function (res) { return res.json(); })
        .then(function (msg) {
            renderMessage(msg);
            const area = document.getElementById('message-content');
            area.scrollTop = area.scrollHeight;
            this.value = '';
        }.bind(this));
    });

    /* ── Helpers ──────────────────────────────────────────────── */
    function resetMessageArea() {
        const area = document.getElementById('message-content');
        area.innerHTML = '';
        area.classList.add('empty-state');
        area.textContent = 'Select a conversation to view messages.';
    }

    function escapeHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

});

function closeInboxReportModal() {
    document.getElementById('inbox-report-modal').classList.remove('open');
}
</script>
@endsection