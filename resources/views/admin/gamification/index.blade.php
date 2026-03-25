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
    }

    .page-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title i {
        font-size: 1.5rem;
        color: #f59e0b;
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

    .page-tag {
        font-size: 0.75rem;
        padding: 4px 12px;
        border-radius: 20px;
        background: #f3f4f6;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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

    .settings-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        padding: 24px;
    }

    @media (max-width: 992px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
    }

    .setting-card {
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        padding: 20px;
    }

    .setting-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
        color: #1f2937;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .setting-header i {
        color: #3b82f6;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-input {
        width: 100%;
        padding: 10px 14px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        background: #fff;
        font-size: 0.875rem;
        color: #1f2937;
        transition: all 0.15s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-textarea {
        resize: none;
        min-height: 80px;
    }

    .form-hint {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-top: 4px;
    }

    .switch-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .switch-row:last-child {
        border-bottom: none;
    }

    .switch-info {
        flex: 1;
    }

    .switch-title {
        font-size: 0.9rem;
        font-weight: 500;
        color: #1f2937;
    }

    .switch-desc {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-top: 2px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #d1d5db;
        transition: .3s;
        border-radius: 24px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #10b981;
    }

    input:checked + .slider:before {
        transform: translateX(20px);
    }

    .badge-count {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 4px;
        background: #eff6ff;
        color: #3b82f6;
        margin-top: 10px;
    }

    .page-actions {
        padding: 16px 24px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
    }

    .btn-save {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        border-radius: 6px;
        border: none;
        background: #3b82f6;
        color: #fff;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-save:hover {
        background: #2563eb;
    }
</style>

<div class="page-container">
    <div class="page-card">
        <div class="page-header">
            <div class="page-title">
                <i class="fas fa-trophy"></i>
                <div>
                    <h2>Gamification Settings</h2>
                    <p>Configure experience points, badges, and rewards</p>
                </div>
            </div>
            <span class="page-tag">Global Settings</span>
        </div>

        @if (session('success'))
            <div class="alert">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.gamification.update') }}" method="POST">
            @csrf
            <div class="settings-grid">
                <!-- XP Mastery -->
                <div class="setting-card">
                    <div class="setting-header">
                        <i class="fas fa-bolt"></i>
                        <span>XP Settings</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lesson Completion Reward</label>
                        <input type="text" name="point_ratio" value="{{ $config['point_ratio'] }}" class="form-input">
                        <p class="form-hint">XP gained after finishing a lesson</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Experience Tiering</label>
                        <select class="form-input">
                            <option>Standard Progression (Linear)</option>
                            <option>Veteran Path (Exponential)</option>
                            <option>Elite Quest (Fixed)</option>
                        </select>
                    </div>
                </div>

                <!-- Badges -->
                <div class="setting-card">
                    <div class="setting-header">
                        <i class="fas fa-medal"></i>
                        <span>Achievement Badges</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Available Badges</label>
                        <textarea name="badges" class="form-input form-textarea" placeholder="Enter badges separated by commas...">{{ implode(', ', $config['badges']) }}</textarea>
                        <p class="form-hint">Separate badge names with commas</p>
                    </div>
                    <div class="badge-count">
                        🎖️ {{ count($config['badges']) }} Active Achievements
                    </div>
                </div>

                <!-- Rankings -->
                <div class="setting-card">
                    <div class="setting-header">
                        <i class="fas fa-star"></i>
                        <span>Rankings & Visibility</span>
                    </div>
                    <div class="switch-row">
                        <div class="switch-info">
                            <div class="switch-title">Global Leaderboard</div>
                            <div class="switch-desc">Allow students to see each other's rankings</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="leaderboard_enabled" {{ $config['leaderboard_enabled'] ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="switch-row">
                        <div class="switch-info">
                            <div class="switch-title">Public Profile XP</div>
                            <div class="switch-desc">Show total XP on student profiles</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="setting-card">
                    <div class="setting-header">
                        <i class="fas fa-bullhorn"></i>
                        <span>Notifications</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Level-Up Message</label>
                        <input type="text" class="form-input" value="Congratulations! You have reached a new level!">
                    </div>
                    <p class="form-hint">
                        <i class="fas fa-info-circle" style="color: #3b82f6;"></i>
                        These alerts motivate students to keep progressing
                    </p>
                </div>
            </div>

            <div class="page-actions">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
