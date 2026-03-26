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
        color: #8b5cf6;
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

    .status-tag {
        font-size: 0.75rem;
        padding: 4px 12px;
        border-radius: 20px;
        background: #d1fae5;
        color: #065f46;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        padding: 24px;
    }

    .module-card {
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        transition: all 0.2s ease;
    }

    .module-card:hover {
        border-color: #d1d5db;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .module-icon {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        background: #eff6ff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        color: #3b82f6;
        font-size: 1.25rem;
    }

    .module-name {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 8px;
    }

    .module-desc {
        font-size: 0.875rem;
        color: #6b7280;
        line-height: 1.5;
        margin-bottom: 16px;
    }

    .module-stats {
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
        font-size: 0.75rem;
        color: #9ca3af;
    }

    .module-stats strong {
        color: #6b7280;
    }

    .switch-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .switch-row:last-child {
        border-bottom: none;
    }

    .switch-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 22px;
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
        border-radius: 22px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #3b82f6;
    }

    input:checked + .slider:before {
        transform: translateX(18px);
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 6px;
        border: none;
        font-size: 0.8rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s ease;
        margin-top: 16px;
    }

    .btn-primary-action {
        background: #3b82f6;
        color: #fff;
    }

    .btn-primary-action:hover {
        background: #2563eb;
    }

    .btn-secondary-action {
        background: #8b5cf6;
        color: #fff;
    }

    .btn-secondary-action:hover {
        background: #7c3aed;
    }

    .btn-full {
        width: 100%;
        justify-content: center;
    }

    .page-footer {
        padding: 16px 24px;
        text-align: center;
        font-size: 0.8rem;
        color: #9ca3af;
        border-top: 1px solid #e5e7eb;
    }

    @media (max-width: 768px) {
        .modules-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-container">
    <div class="page-card">
        <div class="page-header">
            <div class="page-title">
                <i class="fas fa-robot"></i>
                <div>
                    <h2>AI Management</h2>
                    <p>Configure AI-powered features and settings</p>
                </div>
            </div>
            <span class="status-tag">Online</span>
        </div>

        <div class="modules-grid">
            <!-- AI Teacher Toolkit -->
            <div class="module-card">
                <div class="module-icon"><i class="fas fa-magic"></i></div>
                <div class="module-name">AI Assistant Toolkit</div>
                <div class="module-desc">Auto-generates quizzes, reading exercises, and multimedia guides for teachers.</div>

                <div class="switch-row">
                    <span class="switch-label">Quiz Generation</span>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="switch-row">
                    <span class="switch-label">Grading Assistance</span>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="module-stats">
                    Generated Today: <strong>124 Items</strong>
                </div>
            </div>

            <!-- AI-Powered Quests -->
            <div class="module-card">
                <div class="module-icon"><i class="fas fa-dragon"></i></div>
                <div class="module-name">Adaptive Quest Engine</div>
                <div class="module-desc">AI generates personalized literacy challenges that adjust to student performance.</div>

                <div class="switch-row">
                    <span class="switch-label">Difficulty Scaling</span>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="switch-row">
                    <span class="switch-label">Interactive Plots</span>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>

                <button class="btn-action btn-secondary-action">
                    <i class="fas fa-sync-alt"></i> Regenerate Patterns
                </button>
            </div>

            <!-- Neural Analysis -->
            <div class="module-card">
                <div class="module-icon"><i class="fas fa-microchip"></i></div>
                <div class="module-name">Neural Insights</div>
                <div class="module-desc">AI analyzes student output to provide teachers with diagnostic feedback.</div>

                <div class="switch-row">
                    <span class="switch-label">Sentiment Analysis</span>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="switch-row">
                    <span class="switch-label">Skill Gap Mapping</span>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="module-stats">
                    Accuracy: <strong>98.4%</strong>
                </div>
            </div>

            <!-- Personalized Missions -->
            <div class="module-card">
                <div class="module-icon"><i class="fas fa-user-astronaut"></i></div>
                <div class="module-name">Personalized Missions</div>
                <div class="module-desc">Engages students with unique missions and advanced challenges for fast learners.</div>

                <div class="switch-row">
                    <span class="switch-label">Fast Learner Tracks</span>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="switch-row">
                    <span class="switch-label">Scaffolded Support</span>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>

                <button class="btn-action btn-primary-action btn-full">
                    <i class="fas fa-cog"></i> Configure
                </button>
            </div>
        </div>

        <div class="page-footer">
            <i class="fas fa-shield-alt"></i> All AI interactions are monitored and aligned with educational standards.
        </div>
    </div>
</div>

@endsection
