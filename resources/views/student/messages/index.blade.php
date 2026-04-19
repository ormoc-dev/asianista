{{-- resources/views/student/messages/index.blade.php --}}
@extends('student.dashboard')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp

<style>
    .student-party-chat-shell {
        max-width: 1200px;
        margin: 0 auto;
    }

    .student-inbox-scope {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.85);
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(10px);
    }

    .student-inbox-scope .messages-wrapper {
        display: grid;
        grid-template-columns: minmax(260px, 300px) minmax(0, 1fr);
        gap: 0;
        min-height: 560px;
    }

    .student-inbox-scope .messages-sidebar {
        background: linear-gradient(180deg, var(--primary), var(--secondary));
        color: #e0e7ff;
        display: flex;
        flex-direction: column;
        padding: 18px 14px;
        position: relative;
        border-right: 1px solid rgba(255, 255, 255, 0.12);
    }

    .student-inbox-scope .messages-sidebar-header {
        margin-bottom: 14px;
    }

    .student-inbox-scope .messages-sidebar-header h3 {
        font-size: 1.05rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #fff;
        margin-bottom: 6px;
    }

    .student-inbox-scope .messages-sidebar-header h3 i {
        color: var(--accent);
    }

    .student-inbox-scope .inbox-sub {
        font-size: 0.8rem;
        color: rgba(224, 231, 255, 0.85);
        line-height: 1.45;
    }

    .student-inbox-scope .messages-search {
        position: relative;
        margin-bottom: 10px;
    }

    .student-inbox-scope .messages-search input {
        width: 100%;
        padding: 9px 12px 9px 36px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        outline: none;
        font-size: 0.85rem;
        background: rgba(15, 23, 42, 0.35);
        color: #f8fafc;
    }

    .student-inbox-scope .messages-search input::placeholder {
        color: rgba(203, 213, 225, 0.8);
    }

    .student-inbox-scope .messages-search i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--accent);
        font-size: 0.85rem;
    }

    .student-inbox-scope .party-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 10px;
    }

    .student-inbox-scope .party-filter button {
        flex: 1;
        min-width: 0;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 7px 8px;
        font-size: 0.72rem;
        font-weight: 600;
        cursor: pointer;
        background: rgba(15, 23, 42, 0.25);
        color: #e0e7ff;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        transition: background 0.15s, border-color 0.15s;
    }

    .student-inbox-scope .party-filter button.active {
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: var(--text-dark);
        border-color: transparent;
    }

    .student-inbox-scope .messages-list {
        flex: 1;
        overflow-y: auto;
        padding-right: 4px;
        min-height: 100px;
    }

    .student-inbox-scope .messages-list::-webkit-scrollbar {
        width: 5px;
    }

    .student-inbox-scope .messages-list::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.25);
        border-radius: 999px;
    }

    .student-inbox-scope .chat-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 8px;
        border-radius: 12px;
        text-decoration: none;
        color: inherit;
        position: relative;
        transition: background 0.15s;
        border: 1px solid transparent;
    }

    .student-inbox-scope .chat-item:hover {
        background: rgba(15, 23, 42, 0.25);
    }

    .student-inbox-scope .chat-item.active {
        background: rgba(255, 212, 59, 0.2);
        border-color: rgba(255, 212, 59, 0.45);
    }

    .student-inbox-scope .chat-item.active .chat-name,
    .student-inbox-scope .chat-item.active .chat-last {
        color: #fff;
    }

    .student-inbox-scope .chat-avatar,
    .student-inbox-scope .thread-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        position: relative;
        flex-shrink: 0;
        overflow: visible;
    }

    .student-inbox-scope .chat-avatar img,
    .student-inbox-scope .thread-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 212, 59, 0.5);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .student-inbox-scope .chat-status-dot {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid var(--secondary);
    }

    .student-inbox-scope .chat-status-dot.online {
        background: #4ade80;
    }

    .student-inbox-scope .chat-status-dot.offline {
        background: #94a3b8;
    }

    .student-inbox-scope .chat-name {
        font-size: 0.88rem;
        font-weight: 600;
        color: #fff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .student-inbox-scope .role-tag {
        font-size: 0.62rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 2px 6px;
        border-radius: 999px;
        background: rgba(255, 212, 59, 0.25);
        color: var(--accent);
        font-weight: 700;
    }

    .student-inbox-scope .chat-last {
        font-size: 0.75rem;
        color: rgba(224, 231, 255, 0.75);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .student-inbox-scope .chat-meta-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }

    .student-inbox-scope .chat-time {
        font-size: 0.68rem;
        color: rgba(203, 213, 225, 0.7);
        flex-shrink: 0;
    }

    .student-inbox-scope .chat-unread-badge {
        position: absolute;
        right: 6px;
        top: 6px;
        background: var(--accent);
        color: var(--text-dark);
        font-size: 0.65rem;
        font-weight: 700;
        min-width: 18px;
        height: 18px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
    }

    .student-inbox-scope .contacts-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--accent);
        margin: 4px 2px 8px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .student-inbox-scope .contact-start-form button {
        border: none;
        border-radius: 999px;
        padding: 5px 12px;
        font-size: 0.72rem;
        font-weight: 700;
        cursor: pointer;
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: var(--text-dark);
        white-space: nowrap;
    }

    .student-inbox-scope .contacts-search-panel {
        display: none;
        position: absolute;
        left: 14px;
        right: 14px;
        top: 128px;
        z-index: 25;
        max-height: 280px;
        overflow-y: auto;
        background: rgba(15, 23, 42, 0.97);
        border-radius: 14px;
        padding: 10px 8px;
        border: 1px solid rgba(255, 212, 59, 0.25);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45);
    }

    .student-inbox-scope .contacts-search-panel .chat-item:hover {
        background: rgba(255, 255, 255, 0.06);
    }

    .student-inbox-scope .messages-main {
        background: var(--card-bg);
        display: flex;
        flex-direction: column;
    }

    .student-inbox-scope .messages-main-inner {
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 560px;
        padding: 16px 18px 14px;
    }

    .student-inbox-scope .messages-main-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 12px;
        margin-bottom: 10px;
        border-bottom: 2px solid rgba(48, 6, 117, 0.12);
    }

    .student-inbox-scope .thread-user {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .student-inbox-scope .thread-info h3 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-dark);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
    }

    .student-inbox-scope .badge-role {
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 3px 8px;
        border-radius: 999px;
        background: rgba(48, 6, 117, 0.12);
        color: var(--primary);
        font-weight: 700;
    }

    .student-inbox-scope .status-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .student-inbox-scope .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #4ade80;
        box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.35);
    }

    .student-inbox-scope .thread-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .student-inbox-scope .thread-actions button {
        border-radius: 10px;
        border: 1px solid rgba(48, 6, 117, 0.15);
        padding: 6px 10px;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        background: #fff;
        color: var(--primary);
    }

    .student-inbox-scope .thread-actions button.danger {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }

    .student-inbox-scope .messages-thread {
        flex: 1;
        overflow-y: auto;
        padding: 12px 10px;
        background: rgba(255, 255, 255, 0.65);
        border-radius: 14px;
        border: 1px solid rgba(48, 6, 117, 0.1);
        min-height: 260px;
    }

    .student-inbox-scope .messages-thread::-webkit-scrollbar {
        width: 6px;
    }

    .student-inbox-scope .messages-thread::-webkit-scrollbar-thumb {
        background: rgba(48, 6, 117, 0.2);
        border-radius: 999px;
    }

    .student-inbox-scope .msg-row {
        display: flex;
        margin-bottom: 10px;
    }

    .student-inbox-scope .msg-row.me {
        justify-content: flex-end;
    }

    .student-inbox-scope .msg-bubble {
        max-width: 75%;
        padding: 10px 14px;
        border-radius: 16px;
        font-size: 0.88rem;
        line-height: 1.45;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
    }

    .student-inbox-scope .msg-row.me .msg-bubble {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: #f8fafc;
        border-bottom-right-radius: 4px;
    }

    .student-inbox-scope .msg-row.them .msg-bubble {
        background: #fff;
        color: var(--text-dark);
        border: 1px solid rgba(48, 6, 117, 0.1);
        border-bottom-left-radius: 4px;
    }

    .student-inbox-scope .msg-meta {
        font-size: 0.7rem;
        margin-top: 4px;
        text-align: right;
        opacity: 0.9;
    }

    .student-inbox-scope .msg-row.them .msg-meta {
        color: var(--text-muted);
    }

    .student-inbox-scope .empty-thread {
        text-align: center;
        padding: 48px 16px;
        color: var(--text-muted);
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .student-inbox-scope .messages-input-bar {
        margin-top: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-top: 12px;
        border-top: 1px solid rgba(48, 6, 117, 0.12);
    }

    .student-inbox-scope .messages-tools button {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 1px solid rgba(48, 6, 117, 0.15);
        background: #fff;
        color: var(--primary);
        cursor: pointer;
        opacity: 0.5;
        pointer-events: none;
    }

    .student-inbox-scope .messages-input-wrapper {
        flex: 1;
        position: relative;
    }

    .student-inbox-scope .messages-input-wrapper input {
        width: 100%;
        border-radius: 999px;
        border: 1px solid rgba(48, 6, 117, 0.2);
        padding: 10px 100px 10px 16px;
        font-size: 0.88rem;
        outline: none;
        background: #fff;
        color: var(--text-dark);
    }

    .student-inbox-scope .messages-input-wrapper input:focus {
        border-color: var(--accent-dark);
        box-shadow: 0 0 0 3px rgba(255, 212, 59, 0.25);
    }

    .student-inbox-scope .messages-send-btn {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        border-radius: 999px;
        padding: 8px 16px;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    @media (max-width: 900px) {
        .student-inbox-scope .messages-wrapper {
            grid-template-columns: 1fr;
        }

        .student-inbox-scope .messages-sidebar {
            border-right: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .student-inbox-scope .contacts-search-panel {
            position: static;
            margin-top: 8px;
        }

        .student-inbox-scope .messages-main-inner {
            min-height: 420px;
        }
    }
</style>

<div class="dashboard-shell student-party-chat-shell student-inbox-scope">
    <div class="messages-wrapper">

        <div class="messages-sidebar">
            <div class="messages-sidebar-header">
                <h3>
                    <i class="fas fa-comments"></i>
                    Party Chat
                </h3>
                <p class="inbox-sub">Team up with teachers and classmates — same inbox on every device.</p>
            </div>

            <form class="messages-search" method="GET" action="{{ route($routeGroup) }}">
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

            <div id="contacts-search-panel" class="contacts-search-panel">
                <div class="contacts-label">Party contacts</div>

                @forelse($contacts as $contact)
                    @php $online = $contact->isOnline(); @endphp
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
                                    {{ $contact->role === 'teacher' ? 'Mentor' : 'Classmate' }}
                                </div>
                            </div>
                        </div>
                        <form method="POST"
                              action="{{ route($routeGroup.'.start') }}"
                              class="contact-start-form">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $contact->id }}">
                            <button type="submit">Start</button>
                        </form>
                    </div>
                @empty
                    <div style="font-size:0.8rem; color:#cbd5e1; padding:8px 6px;">
                        No contacts yet. Your teacher and classmates appear here when they have accounts.
                    </div>
                @endforelse
            </div>

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

                    <a href="{{ route($routeGroup, ['conversation' => $conversation->id]) }}"
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
                    <div style="font-size:0.82rem; color:rgba(224,231,255,0.8); padding:8px 4px; line-height:1.45;">
                        No party chats yet. Focus the search box above to open contacts.
                    </div>
                @endforelse
            </div>
        </div>

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
                                            ? 'Your mentor'
                                            : 'Party member' }}
                                        @if($online)
                                            · Online
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="thread-actions">
                            <button type="button" class="pin" title="Coming soon" disabled style="opacity:0.5;cursor:not-allowed;">
                                <i class="fas fa-thumbtack"></i>
                            </button>
                            <form method="POST"
                                  action="{{ route($routeGroup.'.destroy', $activeConversation) }}"
                                  onsubmit="return confirm('Clear this chat from your inbox?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="danger" title="Clear chat">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="messages-thread" id="messages-thread">
                        @forelse($messages as $message)
                            @php $isMe = $message->user_id === $user->id; @endphp
                            <div class="msg-row {{ $isMe ? 'me' : 'them' }}" data-message-id="{{ $message->id }}">
                                <div class="msg-bubble">
                                    {{ $message->body }}
                                    <div class="msg-meta">
                                        {{ $isMe ? 'You' : $message->user->name }}
                                        · {{ $message->created_at->format('M d, h:i a') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-thread">
                                Send a message to start the quest log.
                            </div>
                        @endforelse
                    </div>

                    <form method="POST"
                          id="message-send-form"
                          action="{{ route($routeGroup.'.send', $activeConversation) }}"
                          class="messages-input-bar">
                        @csrf
                        <div class="messages-tools">
                            <button type="button" title="Coming soon"><i class="fas fa-paperclip"></i></button>
                        </div>
                        <div class="messages-input-wrapper">
                            <input type="text"
                                   name="body"
                                   placeholder="Type a message to your party…"
                                   autocomplete="off"
                                   required>
                            <button class="messages-send-btn" type="submit">
                                <i class="fas fa-paper-plane"></i>
                                Send
                            </button>
                        </div>
                    </form>
                @else
                    <div class="empty-thread" style="flex:1;display:flex;align-items:center;justify-content:center;">
                        <div>
                            <p style="font-weight:700;color:var(--text-dark);margin-bottom:8px;">Select a conversation</p>
                            <p>Pick someone on the left or search to message your party.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@php
    $__studentMessagesBoot = [
        'pollUrl' => $pollUrl,
        'sendTpl' => $sendUrlTemplate,
        'convUrlTpl' => $conversationUrlTemplate,
        'csrf' => csrf_token(),
        'imagesBase' => asset('images/'),
        'conversationId' => optional($activeConversation)->id,
        'lastMessageId' => (int) ($messages->max('id') ?? 0),
        'pollSince' => (int) ($pollSince ?? 0),
    ];
@endphp
<script type="application/json" id="student-messages-boot">{!! json_encode($__studentMessagesBoot) !!}</script>
<script>
(function () {
    const searchInput   = document.getElementById('messages-search-input');
    const contactsPanel = document.getElementById('contacts-search-panel');
    let activeFilter    = 'all';

    function getChatItems() {
        return document.querySelectorAll('.messages-list .chat-filter-item');
    }

    window.__messagesApplyFilter = function applyFilter() {
        getChatItems().forEach(function (item) {
            const role = (item.dataset.role || '').toLowerCase();
            const show = activeFilter === 'all' || role === activeFilter;
            item.style.display = show ? '' : 'none';
        });
    };

    document.addEventListener('click', function (e) {
        const wrap = e.target.closest('.messages-wrapper');
        if (!wrap) return;
        const btn = e.target.closest('.filter-btn');
        if (!btn) return;
        wrap.querySelectorAll('.filter-btn').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        activeFilter = btn.dataset.filter || 'all';
        window.__messagesApplyFilter();
    });

    const bootEl = document.getElementById('student-messages-boot');
    const boot = bootEl ? JSON.parse(bootEl.textContent) : {};
    const pollUrl = boot.pollUrl || '';
    const sendTpl = boot.sendTpl || '';
    const convUrlTpl = boot.convUrlTpl || '';
    const csrf = boot.csrf || '';
    const imagesBase = boot.imagesBase || '';
    let conversationId = boot.conversationId;
    let lastMessageId = boot.lastMessageId || 0;
    let threadSince = (conversationId && lastMessageId === 0) ? (boot.pollSince || 0) : 0;

    function buildConvUrl(id) {
        return convUrlTpl.replace('__CONV__', String(id));
    }
    function buildSendUrl(id) {
        return sendTpl.replace('__CONV__', String(id));
    }

    function appendMessage(m) {
        const thread = document.getElementById('messages-thread');
        if (!thread || !m || !m.id) return;
        if (thread.querySelector('[data-message-id="' + m.id + '"]')) return;
        const empty = thread.querySelector('.empty-thread');
        if (empty) empty.remove();
        const row = document.createElement('div');
        const isMe = m.is_me === true || m.is_me === 1 || m.is_me === '1';
        row.className = 'msg-row ' + (isMe ? 'me' : 'them');
        row.setAttribute('data-message-id', String(m.id));
        const who = isMe ? 'You' : (m.user_name || '');
        row.innerHTML = '<div class="msg-bubble">' + escapeHtml(m.body) +
            '<div class="msg-meta">' + escapeHtml(who) + ' · ' + escapeHtml(m.created_label) + '</div></div>';
        thread.appendChild(row);
        thread.scrollTop = thread.scrollHeight;
    }

    function escapeHtml(s) {
        if (!s) return '';
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function renderSidebarList(conversations) {
        const list = document.getElementById('messages-list');
        if (!list || !conversations) return;
        let html = '';
        conversations.forEach(function (c) {
            const o = c.other;
            const active = conversationId && c.id === conversationId ? 'active' : '';
            const unread = c.unread > 0
                ? '<span class="chat-unread-badge">' + (c.unread > 9 ? '9+' : c.unread) + '</span>'
                : '';
            const dotClass = o.online ? 'online' : 'offline';
            const tag = o.role === 'teacher' ? '<span class="role-tag">Teacher</span>' : '';
            const pic = imagesBase + (o.pic || 'default-pp.png');
            html += '<a href="' + buildConvUrl(c.id) + '" class="chat-item chat-filter-item ' + active + '" data-role="' + escapeHtml(o.role) + '" data-conv-id="' + c.id + '">' +
                '<div class="chat-avatar"><img src="' + pic + '" alt=""><span class="chat-status-dot ' + dotClass + '"></span></div>' +
                '<div class="chat-text"><div class="chat-name">' + escapeHtml(o.name) + ' ' + tag + '</div>' +
                '<div class="chat-meta-row"><div class="chat-last">' + escapeHtml(c.preview) + '</div>' +
                '<div class="chat-time">' + escapeHtml(c.last_time || '') + '</div></div></div>' + unread + '</a>';
        });
        if (!conversations.length) {
            html = '<div style="font-size:0.82rem;color:rgba(224,231,255,0.8);padding:8px 4px;">No party chats yet.</div>';
        }
        list.innerHTML = html;
        if (typeof window.__messagesApplyFilter === 'function') {
            window.__messagesApplyFilter();
        }
    }

    let pollTimer = null;

    async function poll() {
        if (document.visibilityState === 'hidden') return;
        const params = new URLSearchParams();
        if (searchInput && searchInput.value.trim()) {
            params.set('q', searchInput.value.trim());
        }
        if (conversationId) {
            params.set('conversation', conversationId);
            if (lastMessageId > 0) {
                params.set('after_id', lastMessageId);
            } else if (threadSince > 0) {
                params.set('since', threadSince);
            }
        }
        const url = pollUrl + (params.toString() ? '?' + params.toString() : '');
        try {
            const r = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await r.json();
            if (!data.ok) return;
            if (data.conversations) {
                renderSidebarList(data.conversations);
            }
            if (data.messages && data.messages.length) {
                data.messages.forEach(function (m) {
                    appendMessage(m);
                    lastMessageId = Math.max(lastMessageId, m.id);
                });
                threadSince = 0;
            }
            if (data.cleared_conversation) {
                window.location.reload();
            }
        } catch (e) {
            console.warn('messages poll', e);
        }
    }

    function startPolling() {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(poll, 3000);
        window.__msgPollTimer = pollTimer;
        poll();
    }

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') poll();
    });

    document.addEventListener('turbolinks:before-visit', function () {
        if (window.__msgPollTimer) {
            clearInterval(window.__msgPollTimer);
            window.__msgPollTimer = null;
        }
    });

    function bootMessagesInbox() {
        window.__messagesApplyFilter();

        if (searchInput && contactsPanel && !searchInput.dataset.panelBound) {
            searchInput.dataset.panelBound = '1';
            searchInput.addEventListener('focus', function () {
                contactsPanel.style.display = 'block';
            });
            searchInput.addEventListener('input', function () {
                contactsPanel.style.display = 'block';
            });
            document.addEventListener('click', function (e) {
                const isSearch = searchInput.contains(e.target);
                const isContacts = contactsPanel.contains(e.target);
                if (!isSearch && !isContacts) {
                    contactsPanel.style.display = 'none';
                }
            });
        }

        const sendForm = document.getElementById('message-send-form');
        if (sendForm && conversationId && !sendForm.dataset.ajaxBound) {
            sendForm.dataset.ajaxBound = '1';
            sendForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const input = sendForm.querySelector('input[name="body"]');
                const body = (input && input.value || '').trim();
                if (!body) return;
                const fd = new FormData();
                fd.append('body', body);
                fd.append('_token', csrf);
                try {
                    const res = await fetch(buildSendUrl(conversationId), {
                        method: 'POST',
                        body: fd,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (data.ok && data.message) {
                        input.value = '';
                        appendMessage(data.message);
                        lastMessageId = Math.max(lastMessageId, data.message.id);
                        poll();
                    }
                } catch (err) {
                    console.error(err);
                }
            });
        }
        startPolling();
    }

    if (typeof Turbolinks !== 'undefined') {
        document.addEventListener('turbolinks:load', bootMessagesInbox);
    } else {
        document.addEventListener('DOMContentLoaded', bootMessagesInbox);
    }
})();
</script>
@endsection
