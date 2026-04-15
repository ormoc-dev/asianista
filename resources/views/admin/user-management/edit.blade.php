@extends('admin.dashboard')

@section('content')

<style>
    .um-edit-page {
        padding: 20px;
    }
    .um-edit-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        max-width:100%;
    }
    .um-edit-card .card-header {
        border-bottom: 1px solid var(--border);
    }
    .um-edit-card .card-title {
        font-size: 1.15rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .um-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
    }
    .um-form-group {
        margin-bottom: 20px;
    }
    .um-form-group label,
    .um-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }
    .um-form-group .form-control,
    .um-form-control {
        width: 100%;
        padding: 10px 14px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        background: var(--bg-card);
        font-family: inherit;
        font-size: 0.9rem;
        color: var(--text-primary);
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .um-form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }
    .um-form-group small,
    .um-hint {
        display: block;
        margin-top: 6px;
        font-size: 0.8rem;
        color: var(--text-muted);
        line-height: 1.4;
    }
    .um-student-panel {
        margin-top: 8px;
        padding: 20px;
        border-radius: var(--radius-sm);
        background: var(--bg-main);
        border: 1px solid var(--border);
    }
    .um-student-panel-title {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        margin: 0 0 16px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .um-student-panel-title i {
        color: var(--primary);
    }
    .um-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
    }
    .um-alert-danger {
        padding: 12px 16px;
        border-radius: var(--radius-sm);
        margin-bottom: 20px;
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: #b91c1c;
        font-size: 0.875rem;
    }
    .um-alert-danger ul {
        margin: 0;
        padding-left: 20px;
    }
</style>

<div class="um-edit-page">
    <div class="card um-edit-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-user-edit" style="color: var(--primary);"></i>
                Edit user
            </h2>
        </div>
        <div class="card-body">
            <p style="margin: 0 0 20px 0; font-size: 0.9rem; color: var(--text-secondary);">
                <strong style="color: var(--text-primary);">{{ $user->name }}</strong>
                <span style="color: var(--text-muted);"> · {{ $user->email }}</span>
            </p>

            @if ($errors->any())
                <div class="um-alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.user-management.update', $user->id) }}" method="POST" id="user-edit-form">
                @csrf
                @method('PUT')

                <div class="um-form-grid">
                    <div class="um-form-group">
                        <label class="um-label" for="name">Full name</label>
                        <input type="text" name="name" id="name" class="um-form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="um-form-group">
                        <label class="um-label" for="email">Email</label>
                        <input type="email" name="email" id="email" class="um-form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>

                <div class="um-form-grid">
                    <div class="um-form-group">
                        <label class="um-label" for="user-role-select">Role</label>
                        <select name="role" id="user-role-select" class="um-form-control">
                            <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student</option>
                            <option value="teacher" {{ old('role', $user->role) == 'teacher' ? 'selected' : '' }}>Teacher</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div class="um-form-group">
                        <label class="um-label" for="status">Account status</label>
                        <select name="status" id="status" class="um-form-control">
                            <option value="pending" {{ old('status', $user->status) == 'pending' ? 'selected' : '' }}>Pending approval</option>
                            <option value="approved" {{ old('status', $user->status) == 'approved' ? 'selected' : '' }}>Approved (active)</option>
                            <option value="rejected" {{ old('status', $user->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        <span class="um-hint">Approved users can sign in according to their role.</span>
                    </div>
                </div>

                <div id="student-gameplay-section" class="um-student-panel">
                    <p class="um-student-panel-title">
                        <i class="fas fa-gamepad"></i>
                        Student profile (character &amp; progression)
                    </p>
                    <div class="um-form-grid">
                        <div class="um-form-group" style="margin-bottom: 0;">
                            <label class="um-label" for="character">Character</label>
                            <select name="character" id="character" class="um-form-control">
                                <option value="">None</option>
                                <option value="warrior" {{ old('character', $user->character) == 'warrior' ? 'selected' : '' }}>Warrior</option>
                                <option value="mage" {{ old('character', $user->character) == 'mage' ? 'selected' : '' }}>Mage</option>
                                <option value="rogue" {{ old('character', $user->character) == 'rogue' ? 'selected' : '' }}>Rogue</option>
                                <option value="healer" {{ old('character', $user->character) == 'healer' ? 'selected' : '' }}>Healer</option>
                            </select>
                        </div>
                        <div class="um-form-group" style="margin-bottom: 0;">
                            <label class="um-label" for="level">Level</label>
                            <input type="number" name="level" id="level" class="um-form-control" value="{{ old('level', $user->level ?? 1) }}" min="1">
                        </div>
                        <div class="um-form-group" style="margin-bottom: 0;">
                            <label class="um-label" for="xp">Total XP</label>
                            <input type="number" name="xp" id="xp" class="um-form-control" value="{{ old('xp', $user->xp ?? 0) }}" min="0">
                        </div>
                    </div>
                </div>

                <div class="um-actions">
                    <a href="{{ route('admin.user-management') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var roleSel = document.getElementById('user-role-select');
    var section = document.getElementById('student-gameplay-section');
    if (!roleSel || !section) return;

    function setStudentFieldsActive(isStudent) {
        section.style.display = isStudent ? 'block' : 'none';
        section.querySelectorAll('input, select').forEach(function (el) {
            el.disabled = !isStudent;
        });
    }

    roleSel.addEventListener('change', function () {
        setStudentFieldsActive(roleSel.value === 'student');
    });

    setStudentFieldsActive(roleSel.value === 'student');
})();
</script>

@endsection
