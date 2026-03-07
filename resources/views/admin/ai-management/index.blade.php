@extends('admin.dashboard')

@section('content')

<style>
    .ai-shell {
        margin-top: 10px;
    }

    .ai-card {
        background: radial-gradient(circle at top, rgba(15,23,42,0.8), rgba(30,41,59,0.95));
        border-radius: 20px;
        padding: 25px 30px 35px;
        box-shadow: 0 15px 45px rgba(0,0,0,0.5);
        border: 1px solid rgba(255,255,255,0.1);
        backdrop-filter: blur(15px);
        color: #e2e8f0;
    }

    .ai-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 35px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .ai-title {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .ai-title i {
        font-size: 2.5rem;
        color: #60a5fa;
        text-shadow: 0 0 20px rgba(96,165,250,0.5);
        animation: pulse 2s infinite ease-in-out;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.8; }
        50% { transform: scale(1.05); opacity: 1; }
    }

    .ai-title h2 {
        font-size: 1.6rem;
        font-weight: 700;
        background: linear-gradient(to right, #fff, #94a3b8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .ai-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 25px;
    }

    .ai-module {
        background: rgba(255,255,255,0.03);
        border-radius: 18px;
        padding: 22px;
        border: 1px solid rgba(255,255,255,0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .ai-module::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, transparent, #60a5fa, transparent);
        opacity: 0;
        transition: 0.3s;
    }

    .ai-module:hover {
        background: rgba(255,255,255,0.06);
        transform: translateY(-5px);
        border-color: rgba(96,165,250,0.3);
    }

    .ai-module:hover::before { opacity: 1; }

    .module-icon {
        width: 48px;
        height: 48px;
        background: rgba(96,165,250,0.1);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
        color: #60a5fa;
        font-size: 1.4rem;
    }

    .module-name {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: #f8fafc;
    }

    .module-desc {
        font-size: 0.85rem;
        color: #94a3b8;
        line-height: 1.5;
        margin-bottom: 20px;
    }

    .module-stats {
        display: flex;
        gap: 15px;
        padding-top: 15px;
        border-top: 1px solid rgba(255,255,255,0.05);
    }

    .stat-item {
        font-size: 0.75rem;
        color: #64748b;
    }

    .stat-value {
        font-weight: 600;
        color: #cbd5e1;
    }

    /* Actions */
    .hero-command {
        border: none;
        border-radius: 10px;
        padding: 8px 16px;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .cmd-reforge {
        background: linear-gradient(135deg, #a78bfa, #7c3aed);
        color: white;
        box-shadow: 0 4px 12px rgba(124,58,237,0.3);
    }

    .cmd-activate {
        background: linear-gradient(135deg, #34d399, #059669);
        color: white;
    }

    .hero-command:hover {
        filter: brightness(1.2);
        transform: scale(1.05);
    }

    /* Toggle */
    .switch-group {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 15px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 22px;
    }

    .switch input { opacity: 0; width: 0; height: 0; }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #334155;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px; width: 16px;
        left: 3px; bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider { background-color: #60a5fa; }
    input:checked + .slider:before { transform: translateX(22px); }

    @media (max-width: 768px) {
        .ai-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="ai-shell">
    <div class="ai-card">
        <div class="ai-header">
            <div class="ai-title">
                <i class="fas fa-brain"></i>
                <div>
                    <h2>AI Teacher Assistant Portal</h2>
                    <p style="font-size:0.85rem; color:#94a3b8; font-weight:400;">Oversight and configuration for realm-wide AI neural models.</p>
                </div>
            </div>
            <div style="text-align:right;">
                <span style="font-size:0.75rem; color:#64748b; text-transform:uppercase; letter-spacing:1px; background:rgba(96,165,250,0.1); padding:4px 12px; border-radius:20px;">AI Status: Online</span>
            </div>
        </div>

        <div class="ai-grid">
            <!-- AI Teacher Toolkit -->
            <div class="ai-module">
                <div class="module-icon"><i class="fas fa-magic"></i></div>
                <div class="module-name">AI Assistant Toolkit</div>
                <div class="module-desc">Auto-generates quizzes, reading exercises, and multimedia guides for mentors to reduce record workload.</div>
                
                <div class="switch-group">
                    <span style="font-size:0.8rem; font-weight:600;">Quiz Generation</span>
                    <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                </div>
                <div class="switch-group">
                    <span style="font-size:0.8rem; font-weight:600;">Grading Assistance</span>
                    <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                </div>

                <div class="module-stats" style="margin-top:20px;">
                    <div class="stat-item">Forged Today: <span class="stat-value">124 Items</span></div>
                </div>
            </div>

            <!-- AI-Powered Quests -->
            <div class="ai-module">
                <div class="module-icon"><i class="fas fa-dragon"></i></div>
                <div class="module-name">Adaptive Quest Engine</div>
                <div class="module-desc">AI generates personalized literacy challenges and story-based quests that adjust to hero performance.</div>
                
                <div class="switch-group">
                    <span style="font-size:0.8rem; font-weight:600;">Difficulty Scaling</span>
                    <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                </div>
                <div class="switch-group">
                    <span style="font-size:0.8rem; font-weight:600;">Interactive Plots</span>
                    <label class="switch"><input type="checkbox"><span class="slider"></span></label>
                </div>

                <div style="margin-top:20px; display:flex; gap:10px;">
                    <button class="hero-command cmd-reforge"><i class="fas fa-sync-alt"></i> Reforge Patterns</button>
                </div>
            </div>

            <!-- Neural Analysis -->
            <div class="ai-module">
                <div class="module-icon"><i class="fas fa-microchip"></i></div>
                <div class="module-name">Neural Insights</div>
                <div class="module-desc">AI analyzes student output (written responses) to provide teachers with deep diagnostic feedback.</div>
                
                <div class="switch-group">
                    <span style="font-size:0.8rem; font-weight:600;">Sentiment Analysis</span>
                    <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                </div>
                <div class="switch-group">
                    <span style="font-size:0.8rem; font-weight:600;">Skill Gap Mapping</span>
                    <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                </div>

                <div class="module-stats" style="margin-top:20px;">
                    <div class="stat-item">Accuracy: <span class="stat-value">98.4%</span></div>
                </div>
            </div>

            <!-- Personalized Missions -->
            <div class="ai-module">
                <div class="module-icon"><i class="fas fa-user-astronaut"></i></div>
                <div class="module-name">Personalized Missions</div>
                <div class="module-desc">Engages students with unique missions and prevent boredom by unlocking advanced challenges for fast learners.</div>
                
                <div class="switch-group">
                    <span style="font-size:0.8rem; font-weight:600;">Fast Learner Tracks</span>
                    <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                </div>
                <div class="switch-group">
                    <span style="font-size:0.8rem; font-weight:600;">Scaffolded Support</span>
                    <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                </div>

                <div style="margin-top:20px;">
                    <button class="hero-command cmd-activate" style="width:100%; justify-content:center;"><i class="fas fa-power-off"></i> Configure Neural Link</button>
                </div>
            </div>
        </div>

        <div style="margin-top:30px; border-top:1px solid rgba(255,255,255,0.1); padding-top:20px; color:#64748b; font-size:0.8rem; text-align:center;">
             <i class="fas fa-shield-alt"></i> All AI interactions are monitored and aligned with Smart City competencies.
        </div>
    </div>
</div>

@endsection
