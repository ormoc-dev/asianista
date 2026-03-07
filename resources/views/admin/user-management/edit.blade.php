@extends('admin.dashboard')

@section('content')

<style>
    .edit-user-shell {
        margin-top: 10px;
    }

    .edit-card {
        background: radial-gradient(circle at top, rgba(191,197,219,0.7), rgba(241,241,224,0.9));
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 14px 35px rgba(15,23,42,0.35);
        border: 1px solid rgba(255,255,255,0.7);
        backdrop-filter: blur(10px);
        max-width: auto;
        margin: 0 auto;
    }

    .edit-card-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
        border-bottom: 2px solid rgba(0,35,102,0.1);
        padding-bottom: 15px;
    }

    .edit-card-header i {
        font-size: 1.8rem;
        color: #f5c400;
    }

    .edit-card-header h2 {
        color: #002366;
        margin: 0;
        font-size: 1.5rem;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #475569;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid rgba(0,35,102,0.1);
        background: rgba(255,255,255,0.8);
        font-family: inherit;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #f5c400;
        box-shadow: 0 0 0 3px rgba(245,196,0,0.2);
        background: #fff;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .edit-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 30px;
        border-top: 1px solid rgba(0,35,102,0.05);
        padding-top: 20px;
    }

    .btn-hero {
        padding: 12px 28px;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
    }

    .btn-cancel {
        background: rgba(15,23,42,0.06);
        color: #475569;
        text-decoration: none;
    }

    .btn-cancel:hover {
        background: rgba(15,23,42,0.12);
        color: #0f172a;
    }

    .btn-save {
        background: linear-gradient(135deg, #ffd43b, #f5c400);
        color: #0b1020;
        box-shadow: 0 6px 14px rgba(245,196,0,0.4);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 18px rgba(245,196,0,0.5);
    }

    .alert {
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }

    .alert-danger {
        background: rgba(239,68,68,0.1);
        color: #b91c1c;
        border: 1px solid rgba(239,68,68,0.2);
    }
</style>

<div class="edit-user-shell">
    <div class="edit-card">
        <div class="edit-card-header">
            <i class="fas fa-user-edit"></i>
            <h2>Reforge Hero: {{ $user->name }}</h2>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul style="margin:0; padding-left:20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.user-management.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control">
                        <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="teacher" {{ old('role', $user->role) == 'teacher' ? 'selected' : '' }}>Teacher</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Character Type</label>
                    <select name="character" class="form-control">
                        <option value="">None</option>
                        <option value="warrior" {{ old('character', $user->character) == 'warrior' ? 'selected' : '' }}>Warrior</option>
                        <option value="mage" {{ old('character', $user->character) == 'mage' ? 'selected' : '' }}>Mage</option>
                        <option value="rogue" {{ old('character', $user->character) == 'rogue' ? 'selected' : '' }}>Rogue</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Current Level</label>
                    <input type="number" name="level" class="form-control" value="{{ old('level', $user->level ?? 1) }}" min="1">
                </div>
                <div class="form-group">
                    <label class="form-label">Total EXPERIENCE (XP)</label>
                    <input type="number" name="xp" class="form-control" value="{{ old('xp', $user->xp ?? 0) }}" min="0">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Hero Status (Approval)</label>
                <select name="status" class="form-control">
                    <option value="pending" {{ old('status', $user->status) == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="approved" {{ old('status', $user->status) == 'approved' ? 'selected' : '' }}>Approved (Active)</option>
                    <option value="rejected" {{ old('status', $user->status) == 'rejected' ? 'selected' : '' }}>Rejected (Banned)</option>
                </select>
                <small style="color: #64748b; margin-top: 5px; display: block;">
                    Setting status to "Approved" allows teachers and students to access the realm.
                </small>
            </div>

            <div class="edit-actions">
                <a href="{{ route('admin.user-management') }}" class="btn-hero btn-cancel">
                    <i class="fas fa-times"></i> Retreat
                </a>
                <button type="submit" class="btn-hero btn-save">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
