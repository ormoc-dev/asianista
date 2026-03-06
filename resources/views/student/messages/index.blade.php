{{-- resources/views/student/messages/index.blade.php --}}
@extends('student.dashboard')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp

<style>
    /* ===== MESSAGES LAYOUT ===== */
    .messages-wrapper {
        display: grid;
        grid-template-columns: 290px minmax(0, 1fr);
        gap: 18px;
        height: 540px; /* fits nicely in the shell, adjust if needed */
    }

    /* LEFT: CHAT LIST */
    .messages-sidebar {
        background: linear-gradient(180deg, rgba(15,23,42,0.95), rgba(15,23,42,0.85));
        border-radius: 16px;
        padding: 16px 14px;
        box-shadow: 0 8px 20px rgba(15,23,42,0.5);
        display: flex;
        flex-direction: column;
        color: #e5edff;
        position: relative; /* for contacts dropdown panel */
    }

    .messages-sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .messages-sidebar-header h3 {
        font-size: 0.95rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .messages-sidebar-header h3 i {
        color: var(--accent);
    }

    .messages-sidebar-header span {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
    }

    .messages-search {
        position: relative;
        margin-bottom: 8px;
    }

    .messages-search input {
        width: 100%;
        padding: 8px 30px 8px 30px;
        border-radius: 999px;
        border: none;
        outline: none;
        font-size: 0.85rem;
        background: rgba(15,23,42,0.9);
        color: #e2e8f0;
        box-shadow: inset 0 0 0 1px rgba(148,163,184,0.4);
    }

    .messages-search i {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.85rem;
        color: #94a3b8;
    }

    .messages-search input::placeholder {
        color: #64748b;
    }

    .party-filter {
        display: flex;
        gap: 6px;
        margin-bottom: 10px;
    }

    .party-filter button {
        flex: 1;
        border-radius: 999px;
        border: none;
        padding: 6px 8px;
        font-size: 0.75rem;
        cursor: pointer;
        background: rgba(15,23,42,0.8);
        color: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .party-filter button.active {
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #0b1020;
    }

    .party-filter button i {
        font-size: 0.8rem;
    }

    .messages-list {
        flex: 1;
        margin-top: 6px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .messages-list::-webkit-scrollbar {
        width: 4px;
    }
    .messages-list::-webkit-scrollbar-thumb {
        background: rgba(148,163,184,0.7);
        border-radius: 999px;
    }

    .chat-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 8px;
        border-radius: 10px;
        cursor: pointer;
        transition: background 0.15s ease, transform 0.15s ease;
        position: relative;
        text-decoration: none;
        color: inherit;
    }

    .chat-item:hover {
        background: rgba(15,23,42,0.9);
        transform: translateY(-1px);
    }

    .chat-item.active {
        background: linear-gradient(90deg, rgba(37,99,235,0.9), rgba(30,64,175,0.9));
        box-shadow: 0 0 0 1px rgba(191,219,254,0.7);
    }

    .chat-item.active .chat-name,
    .chat-item.active .chat-last {
        color: #e5edff;
    }

    .chat-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: radial-gradient(circle at 30% 20%, #f1f5f9, #cbd5f5);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(15,23,42,0.7);
        flex-shrink: 0;
        position: relative;
    }

    .chat-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Avatar container */
    .chat-avatar,
    .thread-avatar {
        position: relative;
        width: 48px;     /* your existing size */
        height: 48px;
        border-radius: 50%;
        overflow: visible; /* allow dot OUTSIDE */
    }

    /* The profile image */
    .chat-avatar img,
    .thread-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    /* Status Dot (online/offline) */
    .chat-status-dot,
    .thread-status-dot {
        position: absolute;
        bottom: -3px;        /* OUTSIDE the circle */
        right: -3px;         /* OUTSIDE the circle */
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid #0f172a; /* DARK BLUE BORDER (matches sidebar) */
    }

    /* ONLINE (green) */
    .chat-status-dot.online,
    .thread-status-dot.online {
        background: #22c55e; /* GREEN */
    }

    /* OFFLINE (gray) */
    .chat-status-dot.offline,
    .thread-status-dot.offline {
        background: #9ca3af;
    }

    .chat-text {
        flex: 1;
        min-width: 0;
    }

    .chat-name {
        font-size: 0.85rem;
        font-weight: 600;
        color: #e5edff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .role-tag {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 2px 6px;
        border-radius: 999px;
        background: rgba(15,23,42,0.7);
        color: #facc15;
    }

    .chat-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .chat-last {
        font-size: 0.75rem;
        color: #94a3b8;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .chat-time {
        font-size: 0.7rem;
        color: #64748b;
    }

    .chat-unread-badge {
        position: absolute;
        right: 8px;
        top: 6px;
        background: #f97316;
        color: #0b1020;
        font-size: 0.7rem;
        min-width: 18px;
        height: 18px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        padding: 0 4px;
    }

    .contacts-label {
        font-size: 0.75rem;
        color: #cbd5e1;
        margin: 6px 2px 4px;
    }

    .contact-start-form button {
        border-radius: 999px;
        border: none;
        padding: 4px 10px;
        font-size: 0.7rem;
        cursor: pointer;
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #0b1020;
        box-shadow: 0 3px 8px rgba(15,23,42,0.5);
        white-space: nowrap;
    }

    /* CONTACTS DROPDOWN PANEL (only when search is active) */
    .contacts-search-panel {
        position: absolute;
        left: 14px;
        right: 14px;
        top: 96px; /* under header + search */
        background: rgba(15,23,42,0.98);
        border-radius: 14px;
        padding: 8px 6px 6px;
        box-shadow: 0 16px 40px rgba(15,23,42,0.9);
        max-height: 260px;
        overflow-y: auto;
        display: none;
        z-index: 30;
    }

    .contacts-search-panel::-webkit-scrollbar {
        width: 4px;
    }
    .contacts-search-panel::-webkit-scrollbar-thumb {
        background: rgba(148,163,184,0.7);
        border-radius: 999px;
    }

    /* RIGHT: ACTIVE CHAT */
    .messages-main {
        background: rgba(241,241,224,0.96);
        border-radius: 16px;
        padding: 16px 18px 14px;
        box-shadow: 0 8px 24px rgba(15,23,42,0.35);
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }

    .messages-main::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top left, rgba(255,212,59,0.35), transparent 55%),
                    radial-gradient(circle at bottom right, rgba(56,189,248,0.25), transparent 60%);
        opacity: 0.9;
        pointer-events: none;
    }

    .messages-main-inner {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .messages-main-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 10px;
    }

    .thread-user {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .thread-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.3);
        background: #0b1020;
        flex-shrink: 0;
        position: relative;
    }

    .thread-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .thread-info h3 {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .badge-role {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 2px 6px;
        border-radius: 999px;
        background: rgba(15,23,42,0.07);
        color: #0b1020;
    }

    .thread-info .status-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #22c55e;
        box-shadow: 0 0 0 4px rgba(34,197,94,0.35);
    }

    .thread-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .thread-actions button {
        border-radius: 999px;
        border: none;
        padding: 6px 9px;
        font-size: 0.8rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        background: rgba(15,23,42,0.05);
        color: var(--primary);
    }

    .thread-actions button i {
        font-size: 0.85rem;
    }

    .thread-actions button.pin {
        background: rgba(37,99,235,0.08);
    }

    .thread-actions button.mute {
        background: rgba(148,163,184,0.2);
    }

    .thread-actions button.danger {
        background: rgba(239,68,68,0.12);
        color: #b91c1c;
    }

    /* XP banner style */
    .thread-xp-pill {
        margin-top: 4px;
        padding: 4px 9px;
        border-radius: 999px;
        background: rgba(0,35,102,0.06);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.7rem;
        color: var(--primary);
    }

    .thread-xp-pill i {
        color: var(--accent-dark);
    }

    .thread-xp-track {
        position: relative;
        flex: 1;
        height: 5px;
        border-radius: 999px;
        background: rgba(148,163,184,0.4);
        overflow: hidden;
    }

    .thread-xp-fill {
        position: absolute;
        inset: 0;
        width: 55%;
        background: linear-gradient(90deg, var(--accent), var(--accent-dark));
    }

    /* MESSAGES SCROLL AREA */
    .messages-thread {
        flex: 1;
        margin-top: 4px;
        padding: 10px 6px 10px;
        overflow-y: auto;
    }

    .messages-thread::-webkit-scrollbar {
        width: 6px;
    }
    .messages-thread::-webkit-scrollbar-thumb {
        background: rgba(148,163,184,0.9);
        border-radius: 999px;
    }

    .msg-row {
        display: flex;
        margin-bottom: 8px;
    }

    .msg-row.me {
        justify-content: flex-end;
    }

    .msg-bubble {
        max-width: 70%;
        padding: 8px 10px;
        border-radius: 14px;
        font-size: 0.82rem;
        box-shadow: 0 3px 8px rgba(15,23,42,0.25);
        position: relative;
    }

    .msg-row.me .msg-bubble {
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #0b1020;
        border-bottom-right-radius: 4px;
    }

    .msg-row.them .msg-bubble {
        background: #ffffff;
        color: #0b1020;
        border-bottom-left-radius: 4px;
    }

    .msg-meta {
        font-size: 0.7rem;
        color: #64748b;
        margin-top: 2px;
        text-align: right;
    }

    .msg-row.me .msg-meta {
        color: #334155;
    }

    .empty-thread {
        font-size: 0.85rem;
        color: #6b7280;
        text-align: center;
        padding: 40px 0;
    }

    /* INPUT AREA */
    .messages-input-bar {
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-top: 6px;
        border-top: 1px solid rgba(148,163,184,0.5);
    }

    .messages-tools {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .messages-tools button {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background: rgba(148,163,184,0.25);
        color: var(--primary);
        font-size: 0.85rem;
    }

    .messages-input-wrapper {
        flex: 1;
        position: relative;
    }

    .messages-input-wrapper input {
        width: 100%;
        border-radius: 999px;
        border: none;
        padding: 9px 90px 9px 14px;
        font-size: 0.85rem;
        outline: none;
        background: rgba(248,250,252,0.9);
        box-shadow: inset 0 0 0 1px rgba(148,163,184,0.5);
    }

    .messages-send-btn {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        border-radius: 999px;
        border: none;
        padding: 6px 12px;
        font-size: 0.8rem;
        cursor: pointer;
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #0b1020;
        display: flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 4px 10px rgba(15,23,42,0.35);
    }

    .messages-send-btn i {
        font-size: 0.85rem;
    }

    @media (max-width: 992px) {
        .messages-wrapper {
            grid-template-columns: 1.1fr 2fr;
            height: 520px;
        }
    }

    @media (max-width: 768px) {
        .messages-wrapper {
            grid-template-columns: 1fr;
            height: auto;
        }
        .messages-main {
            margin-top: 14px;
        }
        /* dropdown behaves like a normal block on mobile */
        .contacts-search-panel {
            position: static;
            margin-top: 4px;
            box-shadow: none;
        }
    }
</style>

<div class="messages-wrapper">

    {{-- LEFT: Party / chat list + contacts --}}
    <div class="messages-sidebar">
        <div class="messages-sidebar-header">
            <h3>
                <i class="fas fa-comments"></i>
                Party Chat
            </h3>
            <span>Messages</span>
        </div>

        {{-- search (server-side) --}}
        <form class="messages-search" method="GET" action="{{ route('student.messages') }}">
            <i class="fas fa-search"></i>
            <input
                type="text"
                name="q"
                id="messages-search-input"
                placeholder="Search party members…"
                autocomplete="off"
                value="{{ $search ?? '' }}"
            >
        </form>

        {{-- ⭐ CONTACTS DROPDOWN (shows when search is focused) --}}
        <div id="contacts-search-panel" class="contacts-search-panel">
            <div class="contacts-label">⭐ Your contacts</div>

            @foreach($contacts as $contact)
                @php
                    $online = $contact->isOnline();
                @endphp
                <div class="chat-item chat-filter-item" data-role="{{ $contact->role }}">
                    <div class="chat-avatar">
                        <img src="{{ asset('images/' . ($contact->profile_pic ?? 'default-pp.png')) }}"
                             alt="{{ $contact->name }}">
                        <span class="chat-status-dot {{ $online ? 'online' : 'offline' }}"></span>
                    </div>
                    <div class="chat-text">
                        <div class="chat-name">
                            {{ $contact->name }}
                            @if($contact->role === 'teacher')
                                <span class="role-tag">Teacher</span>
                            @endif
                        </div>
                        <div class="chat-meta-row">
                            <div class="chat-last">
                                {{ $contact->role === 'teacher' ? 'Teacher' : 'Student' }}
                            </div>
                        </div>
                    </div>

                    {{-- start conversation --}}
                    <form method="POST"
                          action="{{ route('student.messages.start') }}"
                          class="contact-start-form">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $contact->id }}">
                        <button type="submit">Start</button>
                    </form>
                </div>
            @endforeach
        </div>

        {{-- filters: All / Teachers / Classmates --}}
        <div class="party-filter">
            <button type="button" class="active filter-btn" data-filter="all">
                <i class="fas fa-users"></i> All
            </button>
            <button type="button" class="filter-btn" data-filter="teacher">
                <i class="fas fa-graduation-cap"></i> Teachers
            </button>
            <button type="button" class="filter-btn" data-filter="student">
                <i class="fas fa-user-friends"></i> Classmates
            </button>
        </div>

        <div class="messages-list" id="messages-list">
            {{-- Existing conversations --}}
            @forelse($conversations as $conversation)
                @php
                    $other = $conversation->participants
                        ->firstWhere('id', '!=', $user->id) ?? $user;

                    $unread   = $conversation->unreadCountFor($user);
                    $isActive = optional($activeConversation)->id === $conversation->id;
                    $online   = $other->isOnline();
                    $last     = $conversation->lastMessage;
                    $lastTime = $last?->created_at?->diffForHumans(null, true) ?? '';
                    $preview  = $last
                        ? (($last->user_id === $user->id ? 'You: ' : $last->user->name . ': ')
                           . Str::limit($last->body, 40))
                        : 'No messages yet';
                @endphp

                <a href="{{ route('student.messages', ['conversation' => $conversation->id]) }}"
                   class="chat-item chat-filter-item {{ $isActive ? 'active' : '' }}"
                   data-role="{{ $other->role }}">
                    <div class="chat-avatar">
                        <img src="{{ asset('images/' . ($other->profile_pic ?? 'default-pp.png')) }}"
                             alt="{{ $other->name }}">
                        <span class="chat-status-dot {{ $online ? 'online' : 'offline' }}"></span>
                    </div>

                    <div class="chat-text">
                        <div class="chat-name">
                            {{ $other->name }}
                            @if($other->role === 'teacher')
                                <span class="role-tag">Teacher</span>
                            @endif
                        </div>
                        <div class="chat-meta-row">
                            <div class="chat-last">{{ $preview }}</div>
                            <div class="chat-time">{{ $lastTime }}</div>
                        </div>
                    </div>

                    @if($unread > 0)
                        <span class="chat-unread-badge">
                            {{ $unread > 9 ? '9+' : $unread }}
                        </span>
                    @endif
                </a>
            @empty
                <div style="font-size:0.8rem; color:#cbd5e1; padding:6px 4px;">
                    No party chats yet. Start a new conversation from your contacts.
                </div>
            @endforelse

            {{-- NOTE: contacts removed from here. They now appear only in the search dropdown. --}}
        </div>
    </div>

    {{-- RIGHT: Active conversation --}}
    <div class="messages-main">
        <div class="messages-main-inner">
            @if($activeConversation)
                @php
                    $other  = $activeConversation->participants
                        ->firstWhere('id', '!=', $user->id) ?? $user;
                    $online = $other->isOnline();
                @endphp

                <div class="messages-main-header">
                    <div class="thread-user">
                        <div class="thread-avatar">
                            <img src="{{ asset('images/' . ($other->profile_pic ?? 'default-pp.png')) }}"
                                 alt="{{ $other->name }}">
                        </div>
                        <div class="thread-info">
                            <h3>
                                {{ $other->name }}
                                <span class="badge-role">
                                    {{ $other->role === 'teacher' ? 'Teacher' : 'Classmate' }}
                                </span>
                            </h3>
                            <div class="status-row">
                                <span class="status-dot"></span>
                                <span>
                                    {{ $other->role === 'teacher'
                                        ? 'Online • Responds quickly'
                                        : 'Party member' }}
                                </span>
                            </div>
                            <div class="thread-xp-pill">
                                <i class="fas fa-bolt"></i>
                                Chat XP
                                <div class="thread-xp-track">
                                    <div class="thread-xp-fill"></div>
                                </div>
                                <span>320 / 500</span>
                            </div>
                        </div>
                    </div>

                    <div class="thread-actions">
                        <button type="button" class="pin">
                            <i class="fas fa-thumbtack"></i> Pin
                        </button>
                        <button type="button" class="mute">
                            <i class="fas fa-bell-slash"></i> Mute
                        </button>
                        <form method="POST"
                              action="{{ route('student.messages.destroy', $activeConversation) }}"
                              onsubmit="return confirm('Delete this conversation?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="danger">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="messages-thread">
                    @forelse($messages as $message)
                        @php $isMe = $message->user_id === $user->id; @endphp
                        <div class="msg-row {{ $isMe ? 'me' : 'them' }}">
                            <div class="msg-bubble">
                                {{ $message->body }}
                                <div class="msg-meta">
                                    {{ $isMe ? 'You' : $message->user->name }}
                                    • {{ $message->created_at->format('M d, h:i a') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-thread">
                            Start the conversation and earn XP for positive communication!
                        </div>
                    @endforelse
                </div>

                <form method="POST"
                      action="{{ route('student.messages.send', $activeConversation) }}"
                      class="messages-input-bar">
                    @csrf
                    <div class="messages-tools">
                        <button type="button" title="Attach file">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <button type="button" title="Send emoji">
                            <i class="fas fa-smile"></i>
                        </button>
                        <button type="button" title="Send sticker">
                            <i class="fas fa-star"></i>
                        </button>
                    </div>

                    <div class="messages-input-wrapper">
                        <input type="text"
                               name="body"
                               placeholder="Type your message and earn XP for positive communication…"
                               autocomplete="off"
                               required>
                        <button class="messages-send-btn" type="submit">
                            <i class="fas fa-paper-plane"></i>
                            Send
                        </button>
                    </div>
                </form>
            @else
                <div class="empty-thread">
                    Select a conversation on the left to begin chatting with your party.
                </div>
            @endif
        </div>
    </div>

</div>

{{-- simple JS: filter by role (All / Teachers / Classmates) + show contacts on search --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const items         = document.querySelectorAll('.chat-filter-item');
        let activeFilter    = 'all';

        const searchInput   = document.getElementById('messages-search-input');
        const contactsPanel = document.getElementById('contacts-search-panel');

        function applyFilter() {
            items.forEach(item => {
                const role = (item.dataset.role || '').toLowerCase();
                const show = activeFilter === 'all' || role === activeFilter;
                item.style.display = show ? '' : 'none';
            });
        }

        filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                filterButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                activeFilter = btn.dataset.filter;
                applyFilter();
            });
        });

        applyFilter();

        // === Contacts dropdown behavior (like Messenger) ===
        if (searchInput && contactsPanel) {
            searchInput.addEventListener('focus', () => {
                contactsPanel.style.display = 'block';
            });

            // Optional: also show when user types
            searchInput.addEventListener('input', () => {
                if (searchInput.value.trim().length >= 0) {
                    contactsPanel.style.display = 'block';
                }
            });

            // Hide panel when clicking outside
            document.addEventListener('click', (e) => {
                const isSearch   = searchInput.contains(e.target);
                const isContacts = contactsPanel.contains(e.target);

                if (!isSearch && !isContacts) {
                    contactsPanel.style.display = 'none';
                }
            });
        }
    });
</script>
@endsection
