@extends('admin.dashboard')

@section('content')

<style>
    .user-shell {
        margin-top: 10px;
    }

    .user-card {
        background: radial-gradient(circle at top, rgba(191,197,219,0.7), rgba(241,241,224,0.9));
        border-radius: 18px;
        padding: 22px 24px 26px;
        box-shadow: 0 14px 35px rgba(15,23,42,0.35);
        border: 1px solid rgba(255,255,255,0.7);
        backdrop-filter: blur(10px);
    }

    .user-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
    }

    .user-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #002366;
    }

    .user-card-title h2 {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .user-card-title span {
        font-size: 0.85rem;
        color: #64748b;
    }

    .user-filters {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-chip {
        border: none;
        border-radius: 999px;
        padding: 6px 14px;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(15,23,42,0.06);
        color: #0f172a;
        transition: all 0.15s ease;
    }

    .filter-chip i {
        font-size: 0.9rem;
    }

    .filter-chip.active,
    .filter-chip:hover {
        background: linear-gradient(135deg, #ffd43b, #f5c400);
        color: #0b1020;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    }

    .user-stats-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .user-stat-pill {
        flex: 1 1 140px;
        background: rgba(255,255,255,0.75);
        border-radius: 999px;
        padding: 6px 14px;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #0b1020;
    }

    .user-stat-pill i {
        color: #f5c400;
    }

    /* table */
.user-table-wrapper {
    margin-top: 10px;
    background: rgba(255,255,255,0.8);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 24px rgba(15,23,42,0.22);
}

.user-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

.user-table thead {
    background: linear-gradient(90deg, #0f172a, #1e293b);
    color: #e2e8f0;
}

.user-table th,
.user-table td {
    padding: 10px 12px;
    text-align: left;      /* center all columns */
    vertical-align: middle;
}

.user-table th {
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

/* keep the "User" column left-aligned */
.user-table th:first-child,
.user-table td:first-child {
    text-align: left;
}

.user-table tbody tr:nth-child(even) {
    background: rgba(15,23,42,0.02);
}

.user-table tbody tr:hover {
    background: rgba(254,249,195,0.6);
}

    .user-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 0 8px rgba(15,23,42,0.35);
        margin-right: 8px;
        flex-shrink: 0;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-main {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .user-main-info {
        display: flex;
        flex-direction: column;
    }

    .user-main-info .name {
        font-size: 0.9rem;
        font-weight: 600;
        color: #0f172a;
    }

    .user-main-info .email {
        font-size: 0.78rem;
        color: #64748b;
    }

    .role-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .role-admin {
        background: rgba(239,68,68,0.12);
        color: #b91c1c;
    }

    .role-teacher {
        background: rgba(59,130,246,0.12);
        color: #1d4ed8;
    }

    .role-student {
        background: rgba(34,197,94,0.12);
        color: #15803d;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 2px 10px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-pending { background: rgba(245,158,11,0.15); color: #b45309; }
    .status-approved { background: rgba(16,185,129,0.15); color: #065f46; }
    .status-rejected { background: rgba(239,68,68,0.15); color: #991b1b; }

    .character-pill {
        font-size: 0.8rem;
        padding: 4px 9px;
        border-radius: 999px;
        background: rgba(15,23,42,0.06);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .character-pill i {
        color: #f59e0b;
        font-size: 0.85rem;
    }

    .xp-label {
        font-size: 0.8rem;
        color: #475569;
    }

    .user-actions {
        display: flex;
        gap: 6px;
    }

    .btn-icon {
        border: none;
        border-radius: 999px;
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.9rem;
        background: rgba(15,23,42,0.06);
        color: #0f172a;
        transition: all 0.15s ease;
    }

    .btn-icon.view:hover {
        background: rgba(59,130,246,0.16);
        color: #1d4ed8;
    }

    .btn-icon.edit:hover {
        background: rgba(234,179,8,0.2);
        color: #b45309;
    }

    .btn-icon.delete:hover {
        background: rgba(248,113,113,0.2);
        color: #b91c1c;
    }

    @media (max-width: 992px) {
        .user-card {
            padding: 18px 16px 22px;
        }

        .user-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .user-stats-row {
            flex-direction: column;
        }

        .user-table th:nth-child(5),
        .user-table td:nth-child(5) {
            display: none; /* hide XP on smaller screens */
        }
    }

    @media (max-width: 768px) {
        main {
            margin-left: 0;
        }
    }
        /* === Delete confirmation modal === */
    .hero-modal-backdrop {
        position: fixed;
        inset: 0;
        background: radial-gradient(circle at top, rgba(251,191,36,0.08), rgba(15,23,42,0.78));
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .hero-modal {
        background: radial-gradient(circle at top, rgba(254,243,199,0.96), rgba(248,250,252,0.98));
        border-radius: 18px;
        padding: 20px 22px 18px;
        width: 100%;
        max-width: 360px;
        box-shadow: 0 18px 40px rgba(15,23,42,0.55);
        border: 1px solid rgba(250,204,21,0.5);
    }

    .hero-modal-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .hero-modal-icon {
        width: 40px;
        height: 40px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at top, #f97373, #b91c1c);
        color: #fefce8;
        box-shadow: 0 0 14px rgba(248,113,113,0.9);
    }

    .hero-modal-title {
        font-weight: 700;
        font-size: 1rem;
        color: #1f2937;
    }

    .hero-modal-body {
        font-size: 0.85rem;
        color: #4b5563;
        margin-bottom: 14px;
    }

    .hero-modal-body strong {
        color: #b91c1c;
    }

    .hero-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 4px;
    }

    .hero-modal-btn {
        border-radius: 999px;
        padding: 6px 14px;
        font-size: 0.8rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }

    .hero-modal-cancel {
        background: rgba(15,23,42,0.06);
        color: #0f172a;
    }

    .hero-modal-cancel:hover {
        background: rgba(15,23,42,0.12);
    }

    .hero-modal-confirm {
        background: linear-gradient(135deg, #f97373, #b91c1c);
        color: #fefce8;
        box-shadow: 0 6px 14px rgba(220,38,38,0.6);
    }

    .hero-modal-confirm:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 18px rgba(220,38,38,0.75);
    }

    .hidden {
        display: none !important;
    }

</style>

<div class="user-shell">
    <div class="user-card">
        <div class="user-card-header">
            <div class="user-card-title">
                <i class="fas fa-users-cog" style="font-size:1.5rem; color:#f5c400;"></i>
                <div>
                    <h2>User Management</h2>
                    <span>Manage all heroes in your realm — teachers and students.</span>
                </div>
            </div>

            <div class="user-filters">
                <button class="filter-chip active" data-filter="all">
                    <i class="fas fa-layer-group"></i> All
                </button>
                <button class="filter-chip" data-filter="teacher">
                    <i class="fas fa-chalkboard-teacher"></i> Teachers
                </button>
                <button class="filter-chip" data-filter="student">
                    <i class="fas fa-user-graduate"></i> Students
                </button>
            </div>
        </div>

        {{-- Success/Status Alert --}}
        @if (session('status'))
            <div style="padding: 12px 18px; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #065f46; border-radius: 12px; margin-bottom: 15px; font-size: 0.9rem; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check-circle"></i>
                {{ session('status') }}
            </div>
        @endif

        {{-- quick stats --}}
        <div class="user-stats-row">
            <div class="user-stat-pill">
                <i class="fas fa-users"></i>
                <span><strong>{{ $users->count() }}</strong> total users</span>
            </div>
            <div class="user-stat-pill">
                <i class="fas fa-chalkboard-teacher"></i>
                <span><strong>{{ $users->where('role','teacher')->count() }}</strong> teachers</span>
            </div>
            <div class="user-stat-pill">
                <i class="fas fa-user-graduate"></i>
                <span><strong>{{ $users->where('role','student')->count() }}</strong> students</span>
            </div>
        </div>

        <div class="user-table-wrapper">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Character</th>
                        <th>Email</th>
                        <th>XP / Level</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="user-row" data-role="{{ $user->role }}">
                            <td>
                                <div class="user-main">
                                    <div class="user-avatar">
                                        @php
                                            $profilePic = $user->profile_pic ?? 'default-pp.png';
                                        @endphp
                                        <img src="{{ asset('images/' . $profilePic) }}" alt="{{ $user->name }}">
                                    </div>
                                    <div class="user-main-info">
                                        <span class="name">{{ $user->name }}</span>
                                        <span class="email">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $roleClass = $user->role === 'admin' ? 'role-admin' : ($user->role === 'teacher' ? 'role-teacher' : 'role-student');
                                @endphp
                                <span class="role-pill {{ $roleClass }}">
                                    @if($user->role === 'admin')
                                        <i class="fas fa-crown"></i>
                                    @elseif($user->role === 'teacher')
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    @else
                                        <i class="fas fa-user-graduate"></i>
                                    @endif
                                    {{ strtoupper($user->role) }}
                                </span>
                            </td>
                            <td>
                                @if ($user->character)
                                    <span class="character-pill">
                                        <i class="fas fa-magic"></i>
                                        {{ ucfirst($user->character) }}
                                    </span>
                                @else
                                    <span style="font-size:0.8rem; color:#94a3b8;">—</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="xp-label">
                                    @php
                                        $xp = $user->xp ?? null;
                                        $level = $user->level ?? null;
                                    @endphp

                                    @if($xp || $level)
                                        Level <strong>{{ $level ?? '01' }}</strong>
                                        @if($xp)
                                            • XP {{ number_format($xp) }}
                                        @endif
                                    @else
                                        <span style="font-size:0.8rem; color:#94a3b8;">No data</span>
                                    @endif
                                </span>
                            </td>
                            <td>
                                @php
                                    $status = $user->status ?? 'pending';
                                    $statusClass = 'status-' . $status;
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    <i class="fas @if($status === 'approved') fa-check-circle @elseif($status === 'rejected') fa-times-circle @else fa-hourglass-half @endif"></i>
                                    {{ $status }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <div class="user-actions">
                                    <a href="{{ route('admin.user-management.show', $user->id) }}" class="btn-icon view" title="View details">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.user-management.edit', $user->id) }}" class="btn-icon edit" title="Edit hero">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.user-management.destroy', $user->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                class="btn-icon delete btn-delete-hero"
                                                data-username="{{ $user->name }}"
                                                title="Delete hero">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:18px; font-size:0.9rem; color:#64748b;">
                                No users found yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{-- Delete hero modal --}}
<div id="deleteHeroModal" class="hero-modal-backdrop hidden">
    <div class="hero-modal">
        <div class="hero-modal-header">
            <div class="hero-modal-icon">
                <i class="fas fa-skull-crossbones"></i>
            </div>
            <div class="hero-modal-title">Banish hero?</div>
        </div>
        <div class="hero-modal-body">
            You are about to remove <strong id="deleteHeroName">this hero</strong> from the realm.
            This cannot be undone. Their progress, XP and records will be lost.
        </div>
        <div class="hero-modal-actions">
            <button type="button" class="hero-modal-btn hero-modal-cancel modal-close-btn">
                <i class="fas fa-undo-alt"></i> Keep hero
            </button>
            <button type="button" class="hero-modal-btn hero-modal-confirm" id="deleteHeroConfirm">
                <i class="fas fa-fire-alt"></i> Banish
            </button>
        </div>
    </div>
</div>


        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --------- filter buttons ----------
        const filterButtons = document.querySelectorAll('.filter-chip');
        const rows = document.querySelectorAll('.user-row');

        filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const filter = btn.getAttribute('data-filter');

                filterButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                rows.forEach(row => {
                    const role = row.getAttribute('data-role');
                    if (filter === 'all' || role === filter) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });

        // --------- modal general behavior ----------
        const modals = document.querySelectorAll('.hero-modal-backdrop');
        const closeBtns = document.querySelectorAll('.modal-close-btn');

        closeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                modals.forEach(m => m.classList.add('hidden'));
                pendingForm = null;
            });
        });

        modals.forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    pendingForm = null;
                }
            });
        });

        // --------- delete hero modal ----------
        const deleteButtons = document.querySelectorAll('.btn-delete-hero');
        const deleteModal = document.getElementById('deleteHeroModal');
        const heroNameSpan = document.getElementById('deleteHeroName');
        const btnConfirmDelete = document.getElementById('deleteHeroConfirm');

        let pendingForm = null;

        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const username = this.getAttribute('data-username') || 'this hero';
                heroNameSpan.textContent = username;
                pendingForm = this.closest('form');
                deleteModal.classList.remove('hidden');
            });
        });

        btnConfirmDelete.addEventListener('click', function () {
            if (pendingForm) {
                pendingForm.submit();
            }
        });
    });
</script>


@endsection
