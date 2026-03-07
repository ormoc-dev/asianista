@extends('admin.dashboard')

@section('content')

<style>
    .gamification-shell {
        margin-top: 10px;
    }

    .mastery-card {
        background: radial-gradient(circle at top, rgba(191,197,219,0.7), rgba(241,241,224,0.9));
        border-radius: 20px;
        padding: 25px 30px 35px;
        box-shadow: 0 15px 40px rgba(15,23,42,0.4);
        border: 1px solid rgba(255,255,255,0.7);
        backdrop-filter: blur(12px);
    }

    .mastery-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid rgba(0,35,102,0.1);
    }

    .mastery-title {
        display: flex;
        align-items: center;
        gap: 15px;
        color: #002366;
    }

    .mastery-title i {
        font-size: 2.2rem;
        color: #f5c400;
        text-shadow: 0 0 15px rgba(245,196,0,0.4);
    }

    .mastery-title h2 {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .settings-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }

    @media (max-width: 992px) {
        .settings-grid { grid-template-columns: 1fr; }
    }

    .setting-box {
        background: rgba(255,255,255,0.7);
        border-radius: 16px;
        padding: 20px;
        border: 1px solid rgba(255,255,255,0.8);
        box-shadow: 0 8px 16px rgba(15,23,42,0.1);
    }

    .setting-box-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
        color: #1e293b;
        font-weight: 700;
        font-size: 1rem;
    }

    .setting-box-header i {
        color: var(--primary);
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .hero-input {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1px solid rgba(0,35,102,0.15);
        background: rgba(255,255,255,0.9);
        font-size: 0.9rem;
        color: #1e293b;
        transition: all 0.2s;
    }

    .hero-input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(245,196,0,0.2);
    }

    .hero-textarea {
        resize: none;
        min-height: 80px;
    }

    /* Toggle Switch */
    .switch-group {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }

    .switch input { opacity: 0; width: 0; height: 0; }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px; width: 18px;
        left: 4px; bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider { background-color: #059669; }
    input:checked + .slider:before { transform: translateX(24px); }

    .mastery-actions {
        margin-top: 30px;
        display: flex;
        justify-content: flex-end;
    }

    .cmd-save {
        background: linear-gradient(135deg, #ffd43b, #f5c400);
        color: #002366;
        border: none;
        border-radius: 12px;
        padding: 12px 30px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 6px 14px rgba(245,196,0,0.4);
        transition: all 0.2s;
    }

    .cmd-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(245,196,0,0.5);
    }

    .alert {
        padding: 12px 18px;
        border-radius: 12px;
        margin-bottom: 25px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideIn 0.3s ease-out;
    }

    .alert-success {
        background: rgba(16,185,129,0.1);
        border: 1px solid rgba(16,185,129,0.2);
        color: #065f46;
    }

    @keyframes slideIn {
        from { transform: translateY(-10px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>

<div class="gamification-shell">
    <div class="mastery-card">
        <div class="mastery-header">
            <div class="mastery-title">
                <i class="fas fa-trophy"></i>
                <div>
                    <h2>Realm Mastery Configuration</h2>
                    <p style="font-size:0.85rem; color:#64748b; font-weight:400;">Fine-tune the experience and rewards for your heroes.</p>
                </div>
            </div>
            <div style="text-align:right;">
                <span style="font-size:0.75rem; color:#94a3b8; text-transform:uppercase; letter-spacing:1px;">Global Realm Settings</span>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.gamification.update') }}" method="POST">
            @csrf
            <div class="settings-grid">
                <!-- XP Mastery -->
                <div class="setting-box">
                    <div class="setting-box-header">
                        <i class="fas fa-bolt"></i>
                        <span>XP Mastery (Point Ratio)</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lesson Completion Reward</label>
                        <input type="text" name="point_ratio" value="{{ $config['point_ratio'] }}" class="hero-input">
                        <p style="font-size:0.7rem; color:#64748b; margin-top:5px;">How much XP a student gains after finishing an ancient scroll.</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Experience Tiering</label>
                        <select class="hero-input">
                            <option>Standard Progression (Linear)</option>
                            <option>Veteran Path (Exponential)</option>
                            <option>Elite Quest (Fixed)</option>
                        </select>
                    </div>
                </div>

                <!-- Legendary Badges -->
                <div class="setting-box">
                    <div class="setting-box-header">
                        <i class="fas fa-medal"></i>
                        <span>Legendary Badges</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Realm Achievements</label>
                        <textarea name="badges" class="hero-input hero-textarea" placeholder="Enter badges separated by commas...">{{ implode(', ', $config['badges']) }}</textarea>
                        <p style="font-size:0.7rem; color:#64748b; margin-top:5px;">List the titles heroes can earn. Separate with commas.</p>
                    </div>
                    <div style="display:flex; gap:8px; margin-top:10px;">
                        <span style="font-size:0.65rem; padding:2px 8px; border-radius:4px; background:rgba(0,35,102,0.05); color:var(--primary);">🎖️ {{ count($config['badges']) }} Active Achievements</span>
                    </div>
                </div>

                <!-- Realm Rankings -->
                <div class="setting-box">
                    <div class="setting-box-header">
                        <i class="fas fa-star"></i>
                        <span>Realm Rankings</span>
                    </div>
                    <div class="switch-group">
                        <div>
                            <div style="font-size:0.9rem; font-weight:600; color:#1e293b;">Global Leaderboard</div>
                            <div style="font-size:0.75rem; color:#64748b;">Allow heroes to see each other's status.</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="leaderboard_enabled" {{ $config['leaderboard_enabled'] ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="switch-group" style="padding-bottom:0;">
                        <div>
                            <div style="font-size:0.9rem; font-weight:600; color:#1e293b;">Public Profile XP</div>
                            <div style="font-size:0.75rem; color:#64748b;">Show total XP on hero identity cards.</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                <!-- Realm Announcements -->
                <div class="setting-box">
                    <div class="setting-box-header">
                        <i class="fas fa-bullhorn"></i>
                        <span>Mastery Notifications</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Level-Up Toast Message</label>
                        <input type="text" class="hero-input" value="Huzzah! You have reached a new rank!">
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-info-circle" style="font-size:0.8rem; color:#3b82f6;"></i>
                        <span style="font-size:0.7rem; color:#64748b;">These alerts motivate heroes to keep pushing their limits.</span>
                    </div>
                </div>
            </div>

            <div class="mastery-actions">
                <button type="submit" class="cmd-save">
                    <i class="fas fa-save"></i> Save Realm Settings
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
