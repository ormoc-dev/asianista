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
        background: transparent;
        border: none;
        box-shadow: none;
        border-radius: 0;
    }

    .student-inbox-scope .messages-wrapper {
        display: grid;
        grid-template-columns: minmax(260px, 300px) minmax(0, 1fr);
        gap: 0;
        min-height: 560px;
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }

    .student-inbox-scope .messages-sidebar {
        background: #312e81;
        color: #e0e7ff;
        display: flex;
        flex-direction: column;
        padding: 18px 14px 16px;
        border-right: 1px solid rgba(255, 255, 255, 0.12);
    }

    .student-inbox-scope .messages-sidebar-header {
        margin-bottom: 14px;
    }

    .student-inbox-scope .messages-sidebar-header h3 {
        font-size: 1.05rem;
        font-weight: 700;
        color: #fff;
        margin: 0 0 4px;
    }

    .student-inbox-scope .inbox-sub {
        font-size: 0.8rem;
        color: rgba(224, 231, 255, 0.78);
        line-height: 1.45;
        margin: 0;
    }

    .student-inbox-scope .messages-search {
        position: relative;
        z-index: 5;
        margin-bottom: 10px;
    }

    .student-inbox-scope .messages-search input {
        width: 100%;
        padding: 9px 12px 9px 34px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        outline: none;
        font-size: 0.85rem;
        background: rgba(0, 0, 0, 0.2);
        color: #f8fafc;
    }

    .student-inbox-scope .messages-search input:focus {
        border-color: rgba(255, 255, 255, 0.35);
    }

    .student-inbox-scope .messages-search input::placeholder {
        color: rgba(203, 213, 225, 0.7);
    }

    .student-inbox-scope .messages-search i {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(253, 230, 138, 0.85);
        font-size: 0.82rem;
    }

    .student-inbox-scope .party-filter {
        display: flex;
        gap: 4px;
        margin-bottom: 10px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    }

    .student-inbox-scope .party-filter button {
        flex: 1;
        min-width: 0;
        border: none;
        border-bottom: 2px solid transparent;
        margin-bottom: -1px;
        padding: 8px 4px;
        font-size: 0.7rem;
        font-weight: 600;
        cursor: pointer;
        background: none;
        color: rgba(224, 231, 255, 0.65);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .student-inbox-scope .party-filter button i {
        font-size: 0.7rem;
        opacity: 0.85;
    }

    .student-inbox-scope .party-filter button.active {
        color: #fde68a;
        border-bottom-color: #fde68a;
    }

    .student-inbox-scope .messages-list {
        position: relative;
        z-index: 1;
        flex: 1;
        overflow-y: auto;
        padding-right: 4px;
        min-height: 120px;
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
        padding: 8px 6px;
        border-radius: 0;
        text-decoration: none;
        color: inherit;
        position: relative;
        transition: background 0.15s;
        border: none;
        border-left: 3px solid transparent;
    }

    .student-inbox-scope .chat-item:hover {
        background: rgba(255, 255, 255, 0.06);
    }

    .student-inbox-scope .chat-item.active {
        background: rgba(255, 255, 255, 0.1);
        border-left-color: #fde68a;
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
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 700;
    }

    .student-inbox-scope .chat-avatar {
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #fff;
    }

    .student-inbox-scope .thread-avatar {
        background: #e2e8f0;
        border: 1px solid var(--border, #e2e8f0);
        color: var(--primary);
    }

    .student-inbox-scope .chat-avatar i,
    .student-inbox-scope .thread-avatar i {
        pointer-events: none;
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
        top: 158px;
        z-index: 30;
        max-height: 280px;
        overflow-y: auto;
        background: #1e1b4b;
        border-radius: 8px;
        padding: 10px 8px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25);
    }

    .student-inbox-scope .contacts-search-panel .chat-item:hover {
        background: rgba(255, 255, 255, 0.06);
    }

    .student-inbox-scope .messages-main {
        background: #fff;
        display: flex;
        flex-direction: column;
    }

    .student-inbox-scope .messages-main-inner {
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 560px;
        padding: 20px 22px 18px;
    }

    .student-inbox-scope .messages-main-empty {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 24px 16px;
        min-height: 320px;
        max-width: 360px;
        margin: 0 auto;
    }

    .student-inbox-scope .messages-main-empty-title {
        margin: 0 0 8px;
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--text-dark);
    }

    .student-inbox-scope .messages-main-empty-text {
        margin: 0;
        font-size: 0.9rem;
        line-height: 1.55;
        color: var(--text-muted);
    }

    .student-inbox-scope .sidebar-list-empty {
        padding: 12px 6px;
        color: rgba(224, 231, 255, 0.75);
        font-size: 0.82rem;
        line-height: 1.45;
        text-align: center;
    }

    .student-inbox-scope .sidebar-list-empty--compact {
        padding: 10px 4px;
    }

    .student-inbox-scope .contacts-empty-hint {
        font-size: 0.78rem;
        color: rgba(203, 213, 225, 0.85);
        padding: 8px 4px;
        line-height: 1.45;
    }

    .student-inbox-scope .messages-main-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 12px;
        margin-bottom: 10px;
        border-bottom: 1px solid var(--border, #e2e8f0);
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
        border-radius: 8px;
        border: 1px solid var(--border, #e2e8f0);
        padding: 6px 10px;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        background: #fff;
        color: var(--primary);
    }

    .student-inbox-scope .thread-actions button.danger {
        background: #fff;
        border-color: #fecaca;
        color: #b91c1c;
    }

    .student-inbox-scope .messages-thread {
        flex: 1;
        overflow-y: auto;
        padding: 8px 4px 12px;
        background: transparent;
        border: none;
        border-radius: 0;
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
        border-radius: 14px;
        font-size: 0.88rem;
        line-height: 1.45;
    }

    .student-inbox-scope .msg-row.me .msg-bubble {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: #f8fafc;
        border-bottom-right-radius: 4px;
    }

    .student-inbox-scope .msg-row.them .msg-bubble {
        background: #f1f5f9;
        color: var(--text-dark);
        border: none;
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

    .student-inbox-scope .empty-thread--thread {
        padding: 24px 12px;
        background: transparent;
        border: none;
    }

    .student-inbox-scope .messages-input-bar {
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-top: 12px;
        border-top: 1px solid var(--border, #e2e8f0);
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
        border-color: var(--primary, #4f46e5);
        outline: none;
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
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .student-inbox-scope .contacts-search-panel {
            position: static;
            margin-top: 8px;
            left: auto;
            right: auto;
            top: auto;
        }

        .student-inbox-scope .messages-main-inner {
            min-height: 420px;
        }

        .student-inbox-scope .party-filter {
            flex-wrap: wrap;
        }
    }
</style>

<div class="dashboard-shell student-party-chat-shell student-inbox-scope">
    <div class="messages-wrapper">

        <div class="messages-sidebar">
            <div class="messages-sidebar-header">
                <h3>Party Chat</h3>
                <p class="inbox-sub">Teachers and classmates in one inbox.</p>
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
                            <i class="fas fa-user"></i>
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
                              class="contact-start-form js-contact-start">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $contact->id }}">
                            <button type="submit">Start</button>
                        </form>
                    </div>
                @empty
                    <div class="contacts-empty-hint">
                        No contacts yet. When your teacher and classmates are on the roster, they show up here.
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
                       data-conv-id="{{ $conversation->id }}"
                       data-role="{{ $other->role }}">
                        <div class="chat-avatar">
                            <i class="fas fa-user"></i>
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
                    <div class="sidebar-list-empty sidebar-list-empty--compact">
                        No conversations yet. Use search to find someone and start a chat.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="messages-main">
            <div class="messages-main-inner">
                <div id="messages-main-panel">
                @if($activeConversation)
                    @php
                        $other  = $activeConversation->participants
                            ->firstWhere('id', '!=', $user->id) ?? $user;
                        $online = $other->isOnline();
                    @endphp

                    <div class="messages-main-header">
                        <div class="thread-user">
                            <div class="thread-avatar">
                                <i class="fas fa-user"></i>
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
                            <div class="empty-thread empty-thread--thread">
                                Send a message to start the conversation.
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
                    <div class="messages-main-empty">
                        <h2 class="messages-main-empty-title">Select a conversation</h2>
                        <p class="messages-main-empty-text">Pick a thread on the left, or search for a teacher or classmate to start a new chat.</p>
                    </div>
                @endif
                </div>
            </div>
        </div>

    </div>
</div>

@php
    $__studentMessagesBoot = [
        'pollUrl' => $pollUrl,
        'sendTpl' => $sendUrlTemplate,
        'convUrlTpl' => $conversationUrlTemplate,
        'threadTpl' => $threadUrlTemplate,
        'destroyTpl' => $destroyUrlTemplate,
        'indexUrl' => $messagesIndexUrl,
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
    const searchForm    = document.querySelector('.student-inbox-scope .messages-search');
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
        if (!wrap || !wrap.closest('.student-inbox-scope')) return;
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
    const threadTpl = boot.threadTpl || '';
    const destroyTpl = boot.destroyTpl || '';
    const indexUrl = boot.indexUrl || '';
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
    function buildThreadUrl(id) {
        return threadTpl.replace('__CONV__', String(id));
    }
    function buildDestroyUrl(id) {
        return destroyTpl.replace('__CONV__', String(id));
    }

    function pushConversationUrl(id) {
        try {
            const u = new URL(indexUrl || window.location.pathname, window.location.origin);
            if (id) {
                u.searchParams.set('conversation', String(id));
            } else {
                u.searchParams.delete('conversation');
            }
            history.pushState({ studentConv: id || null }, '', u.pathname + u.search);
        } catch (err) {
            console.warn('pushState', err);
        }
    }

    function setSidebarActive(id) {
        document.querySelectorAll('#messages-list .chat-item').forEach(function (a) {
            const cid = a.dataset.convId;
            a.classList.toggle('active', id != null && String(cid) === String(id));
        });
    }

    function escapeHtml(s) {
        if (s == null) return '';
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function buildStudentThreadHtml(conv, messages) {
        const o = conv.other;
        const roleLabel = o.role === 'teacher' ? 'Teacher' : 'Classmate';
        const statusLine = o.role === 'teacher' ? 'Your mentor' : 'Party member';
        const onlineExtra = o.online ? ' · Online' : '';
        const sendUrl = buildSendUrl(conv.id);
        let msgs = '';
        messages.forEach(function (m) {
            const isMe = m.is_me === true || m.is_me === 1 || m.is_me === '1';
            msgs += '<div class="msg-row ' + (isMe ? 'me' : 'them') + '" data-message-id="' + m.id + '"><div class="msg-bubble">' +
                escapeHtml(m.body) + '<div class="msg-meta">' + escapeHtml(isMe ? 'You' : (m.user_name || '')) +
                ' · ' + escapeHtml(m.created_label) + '</div></div></div>';
        });
        if (!messages.length) {
            msgs = '<div class="empty-thread empty-thread--thread">Send a message to start the conversation.</div>';
        }
        return (
            '<div class="messages-main-header">' +
            '<div class="thread-user">' +
            '<div class="thread-avatar"><i class="fas fa-user"></i></div>' +
            '<div class="thread-info"><h3>' + escapeHtml(o.name) + ' <span class="badge-role">' + escapeHtml(roleLabel) + '</span></h3>' +
            '<div class="status-row"><span class="status-dot"></span><span>' + escapeHtml(statusLine) + onlineExtra + '</span></div></div></div>' +
            '<div class="thread-actions">' +
            '<button type="button" class="pin" disabled style="opacity:0.5;cursor:not-allowed" title="Coming soon"><i class="fas fa-thumbtack"></i></button>' +
            '<button type="button" class="danger js-clear-thread" title="Clear chat"><i class="fas fa-trash-alt"></i></button></div></div>' +
            '<div class="messages-thread" id="messages-thread">' + msgs + '</div>' +
            '<form method="POST" id="message-send-form" class="messages-input-bar" action="' + escapeHtml(sendUrl) + '">' +
            '<input type="hidden" name="_token" value="' + escapeHtml(csrf) + '">' +
            '<div class="messages-tools"><button type="button" title="Coming soon"><i class="fas fa-paperclip"></i></button></div>' +
            '<div class="messages-input-wrapper">' +
            '<input type="text" name="body" placeholder="Type a message to your party…" autocomplete="off" required>' +
            '<button class="messages-send-btn" type="submit"><i class="fas fa-paper-plane"></i> Send</button></div></form>'
        );
    }

    function showEmptyPanel(opts) {
        opts = opts || {};
        const panel = document.getElementById('messages-main-panel');
        if (!panel) return;
        panel.innerHTML =
            '<div class="messages-main-empty">' +
            '<h2 class="messages-main-empty-title">Select a conversation</h2>' +
            '<p class="messages-main-empty-text">Pick a thread on the left, or search for a teacher or classmate to start a new chat.</p></div>';
        conversationId = null;
        lastMessageId = 0;
        threadSince = 0;
        setSidebarActive(null);
        if (!opts.skipHistory) {
            pushConversationUrl(null);
        }
    }

    async function loadConversation(convId, opts) {
        opts = opts || {};
        const id = parseInt(convId, 10);
        if (!id) return;
        const panel = document.getElementById('messages-main-panel');
        if (!panel || !threadTpl) return;
        try {
            const r = await fetch(buildThreadUrl(id), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!r.ok) return;
            const data = await r.json();
            if (!data.ok) return;
            const conv = data.conversation;
            const messages = data.messages || [];
            panel.innerHTML = buildStudentThreadHtml(conv, messages);
            conversationId = conv.id;
            lastMessageId = messages.reduce(function (acc, m) { return Math.max(acc, m.id); }, 0);
            threadSince = (conversationId && lastMessageId === 0) ? Math.floor(Date.now() / 1000) : 0;
            const thread = document.getElementById('messages-thread');
            if (thread) thread.scrollTop = thread.scrollHeight;
            if (!opts.skipHistory) {
                pushConversationUrl(conversationId);
            }
            setSidebarActive(conversationId);
        } catch (e) {
            console.error('loadConversation', e);
        }
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
            html += '<a href="' + buildConvUrl(c.id) + '" class="chat-item chat-filter-item ' + active + '" data-role="' + escapeHtml(o.role) + '" data-conv-id="' + c.id + '">' +
                '<div class="chat-avatar"><i class="fas fa-user"></i><span class="chat-status-dot ' + dotClass + '"></span></div>' +
                '<div class="chat-text"><div class="chat-name">' + escapeHtml(o.name) + ' ' + tag + '</div>' +
                '<div class="chat-meta-row"><div class="chat-last">' + escapeHtml(c.preview) + '</div>' +
                '<div class="chat-time">' + escapeHtml(c.last_time || '') + '</div></div></div>' + unread + '</a>';
        });
        if (!conversations.length) {
            html = '<div class="sidebar-list-empty sidebar-list-empty--compact">No conversations yet. Use search to find someone and start a chat.</div>';
        }
        if (list.innerHTML === html) {
            if (typeof window.__messagesApplyFilter === 'function') {
                window.__messagesApplyFilter();
            }
            return;
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
                showEmptyPanel({ skipHistory: true });
                pushConversationUrl(null);
            }
        } catch (e) {
            console.warn('messages poll', e);
        }
    }

    function startPolling() {
        if (window.__msgPollTimer) {
            clearInterval(window.__msgPollTimer);
            window.__msgPollTimer = null;
        }
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(poll, 2500);
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

    document.addEventListener('click', function (e) {
        const scope = e.target.closest('.student-inbox-scope');
        if (!scope) return;
        const link = e.target.closest('#messages-list a.chat-item');
        if (link) {
            e.preventDefault();
            const cid = link.dataset.convId;
            if (cid) loadConversation(cid, {});
            return;
        }
        if (e.target.closest('.js-clear-thread')) {
            e.preventDefault();
            if (!conversationId || !confirm('Clear this chat from your inbox?')) return;
            const fd = new FormData();
            fd.append('_token', csrf);
            fd.append('_method', 'DELETE');
            fetch(buildDestroyUrl(conversationId), {
                method: 'POST',
                body: fd,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (r) { return r.json(); }).then(function (data) {
                if (data && data.ok) {
                    showEmptyPanel({});
                    poll();
                }
            }).catch(function (err) { console.error(err); });
        }
    });

    document.addEventListener('submit', function (e) {
        const scope = e.target.closest('.student-inbox-scope');
        if (!scope) return;
        const form = e.target;
        if (form.classList && form.classList.contains('js-contact-start')) {
            e.preventDefault();
            const fd = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: fd,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (r) { return r.json(); }).then(function (data) {
                if (data && data.ok && data.conversation_id) {
                    if (contactsPanel) contactsPanel.style.display = 'none';
                    loadConversation(data.conversation_id, {});
                    poll();
                }
            }).catch(function (err) { console.error(err); });
            return;
        }
        if (form.id === 'message-send-form') {
            e.preventDefault();
            if (!conversationId) return;
            const input = form.querySelector('input[name="body"]');
            const body = (input && input.value || '').trim();
            if (!body) return;
            const fd = new FormData();
            fd.append('body', body);
            fd.append('_token', csrf);
            fetch(buildSendUrl(conversationId), {
                method: 'POST',
                body: fd,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (res) { return res.json(); }).then(function (data) {
                if (data.ok && data.message) {
                    input.value = '';
                    appendMessage(data.message);
                    lastMessageId = Math.max(lastMessageId, data.message.id);
                    poll();
                }
            }).catch(function (err) { console.error(err); });
        }
    });

    window.addEventListener('popstate', function () {
        const params = new URLSearchParams(window.location.search);
        const c = params.get('conversation');
        if (c) {
            loadConversation(c, { skipHistory: true });
        } else {
            showEmptyPanel({ skipHistory: true });
        }
    });

    function bootMessagesInbox() {
        window.__messagesApplyFilter();

        if (searchForm && !searchForm.dataset.ajaxSearch) {
            searchForm.dataset.ajaxSearch = '1';
            searchForm.addEventListener('submit', function (e) {
                e.preventDefault();
                poll();
            });
        }

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
