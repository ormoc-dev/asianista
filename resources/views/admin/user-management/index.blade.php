@extends('admin.dashboard')

@section('content')

<style>
    .page-container {
        padding: 20px;
    }

    .page-card {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
        flex-wrap: wrap;
        gap: 16px;
    }

    .page-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title i {
        font-size: 1.5rem;
        color: #3b82f6;
    }

    .page-title h2 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .page-title p {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 4px 0 0;
    }

    .filter-group {
        display: flex;
        gap: 8px;
    }

    .filter-btn {
        padding: 6px 14px;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        background: #fff;
        font-size: 0.875rem;
        font-weight: 500;
        color: #6b7280;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }

    .filter-btn:hover {
        border-color: #d1d5db;
        background: #f9fafb;
    }

    .filter-btn.active {
        background: #3b82f6;
        border-color: #3b82f6;
        color: #fff;
    }

    .role-filter-select {
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        background: #fff;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        min-width: 160px;
        cursor: pointer;
    }

    .role-filter-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    .alert {
        margin: 16px 24px;
        padding: 12px 16px;
        border-radius: 6px;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 8px;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    .stats-row {
        display: flex;
        gap: 12px;
        padding: 16px 24px;
        border-bottom: 1px solid #e5e7eb;
        flex-wrap: wrap;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #f9fafb;
        border-radius: 6px;
        font-size: 0.875rem;
        color: #6b7280;
    }

    .stat-item strong {
        color: #1f2937;
    }

    .stat-item i {
        color: #9ca3af;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .data-table thead {
        background: #f9fafb;
    }

    .data-table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .data-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .data-table tbody tr:hover {
        background: #f9fafb;
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #e5e7eb;
    }

    .user-info .name {
        font-weight: 500;
        color: #1f2937;
    }

    .user-info .email {
        font-size: 0.75rem;
        color: #9ca3af;
    }

    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
    }

    .role-admin {
        background: #fee2e2;
        color: #991b1b;
    }

    .role-teacher {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .role-student {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: capitalize;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-approved {
        background: #d1fae5;
        color: #065f46;
    }

    .status-rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .action-btns {
        display: flex;
        gap: 6px;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #6b7280;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-icon:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }

    .btn-icon.view:hover {
        background: #eff6ff;
        border-color: #3b82f6;
        color: #3b82f6;
    }

    .btn-icon.edit:hover {
        background: #fffbeb;
        border-color: #f59e0b;
        color: #f59e0b;
    }

    .btn-icon.delete:hover {
        background: #fef2f2;
        border-color: #ef4444;
        color: #ef4444;
    }

    .page-footer {
        padding: 16px 24px;
        text-align: center;
        font-size: 0.8rem;
        color: #9ca3af;
        border-top: 1px solid #e5e7eb;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 2.5rem;
        color: #d1d5db;
        margin-bottom: 12px;
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .modal-box {
        background: #fff;
        padding: 24px;
        border-radius: 8px;
        width: 100%;
        max-width: 360px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .modal-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #fee2e2;
        color: #ef4444;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .modal-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
    }

    .modal-body {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .modal-body strong {
        color: #ef4444;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .btn-cancel {
        padding: 8px 16px;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        background: #fff;
        font-size: 0.875rem;
        font-weight: 500;
        color: #6b7280;
        cursor: pointer;
    }

    .btn-cancel:hover {
        background: #f9fafb;
    }

    .btn-confirm {
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        background: #ef4444;
        font-size: 0.875rem;
        font-weight: 500;
        color: #fff;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-confirm:hover {
        background: #dc2626;
    }

    .hidden {
        display: none !important;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .filter-group {
            width: 100%;
            justify-content: flex-start;
        }
    }
</style>

<div class="page-container">
    <div class="page-card">
        <div class="page-header">
            <div class="page-title">
                <i class="fas fa-users"></i>
                <div>
                    <h2>User Management</h2>
                    <p>Manage all users in the system</p>
                </div>
            </div>

            <div class="filter-group">
                <label for="userRoleFilter" class="sr-only">Filter by role</label>
                <select id="userRoleFilter" class="role-filter-select" title="Show users">
                    <option value="all" selected>All users</option>
                    <option value="teacher">Teachers only</option>
                    <option value="student">Students only</option>
                </select>
                <button type="button" class="filter-btn active" data-filter="all">
                    <i class="fas fa-layer-group"></i> All
                </button>
                <button type="button" class="filter-btn" data-filter="teacher">
                    <i class="fas fa-chalkboard-teacher"></i> Teachers
                </button>
                <button type="button" class="filter-btn" data-filter="student">
                    <i class="fas fa-user-graduate"></i> Students
                </button>
            </div>
        </div>

        @if (session('status'))
            <div class="alert">
                <i class="fas fa-check-circle"></i>
                {{ session('status') }}
            </div>
        @endif

        <div class="stats-row">
            <div class="stat-item">
                <i class="fas fa-users"></i>
                <span><strong>{{ $users->count() }}</strong> total users</span>
            </div>
            <div class="stat-item">
                <i class="fas fa-chalkboard-teacher"></i>
                <span><strong>{{ $users->where('role', 'teacher')->count() }}</strong> teachers</span>
            </div>
            <div class="stat-item">
                <i class="fas fa-user-graduate"></i>
                <span><strong>{{ $users->where('role', 'student')->count() }}</strong> students</span>
            </div>
        </div>

        <div class="table-wrapper" style="padding: 0 24px 24px;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="user-row" data-role="{{ $user->role }}">
                            <td>
                                <div class="user-cell">
                                    <img src="{{ asset('images/' . ($user->profile_pic ?? 'default-pp.png')) }}" class="user-avatar" alt="{{ $user->name }}">
                                    <div class="user-info">
                                        <div class="name">{{ $user->name }}</div>
                                        <div class="email">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $roleClass = $user->role === 'teacher' ? 'role-teacher' : 'role-student';
                                @endphp
                                <span class="role-badge {{ $roleClass }}">
                                    @if($user->role === 'teacher')
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    @else
                                        <i class="fas fa-user-graduate"></i>
                                    @endif
                                    {{ $user->role }}
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
                            <td style="text-align: right;">
                                <div class="action-btns" style="justify-content: flex-end;">
                                    <a href="{{ route('admin.user-management.show', $user->id) }}" class="btn-icon view" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.user-management.edit', $user->id) }}" class="btn-icon edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.user-management.destroy', $user->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-icon delete btn-delete" data-username="{{ $user->name }}" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <p>No users found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="page-footer">
            Total: <strong>{{ $users->count() }}</strong> users
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="modal-overlay hidden">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="modal-title">Delete User?</div>
        </div>
        <div class="modal-body">
            You are about to remove <strong id="deleteUserName">this user</strong>. This action cannot be undone.
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-cancel modal-close">Cancel</button>
            <button type="button" class="btn-confirm" id="confirmDelete">
                <i class="fas fa-trash-alt"></i> Delete
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const roleSelect = document.getElementById('userRoleFilter');
        const rows = document.querySelectorAll('.user-row');

        function applyRoleFilter(filter) {
            rows.forEach(row => {
                const role = row.getAttribute('data-role');
                row.style.display = (filter === 'all' || role === filter) ? '' : 'none';
            });
            filterButtons.forEach(b => {
                b.classList.toggle('active', b.getAttribute('data-filter') === filter);
            });
            if (roleSelect && roleSelect.value !== filter) {
                roleSelect.value = filter;
            }
        }

        filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                applyRoleFilter(btn.getAttribute('data-filter'));
            });
        });

        if (roleSelect) {
            roleSelect.addEventListener('change', function () {
                applyRoleFilter(this.value);
            });
        }

        const deleteModal = document.getElementById('deleteModal');
        const deleteButtons = document.querySelectorAll('.btn-delete');
        const deleteUserName = document.getElementById('deleteUserName');
        const confirmDelete = document.getElementById('confirmDelete');
        const closeButtons = document.querySelectorAll('.modal-close');

        let pendingForm = null;

        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                deleteUserName.textContent = this.getAttribute('data-username') || 'this user';
                pendingForm = this.closest('form');
                deleteModal.classList.remove('hidden');
            });
        });

        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                deleteModal.classList.add('hidden');
                pendingForm = null;
            });
        });

        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) {
                deleteModal.classList.add('hidden');
                pendingForm = null;
            }
        });

        confirmDelete.addEventListener('click', function () {
            if (pendingForm) {
                pendingForm.submit();
            }
        });
    });
</script>

@endsection
