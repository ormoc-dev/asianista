<div class="gamification-container" style="padding: 20px;">
    <!-- Modern Styling -->
    <style>
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .header-section h1 {
            color: var(--primary);
            font-size: 2rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn-create {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .card h2 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 1.5rem;
            text-align: center;
        }
        .leaderboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .top-performer {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            border: 1px solid #e2e8f0;
            transition: transform 0.2s;
        }
        .top-performer:hover {
            transform: translateY(-5px);
        }
        .avatar-container {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
        }
        .avatar-container img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3px solid #4f46e5;
        }
        .crown {
            position: absolute;
            top: -15px;
            right: -10px;
            font-size: 1.5rem;
        }
        .xp-bar {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 10px;
            margin: 10px 0;
            overflow: hidden;
        }
        .xp-progress {
            height: 100%;
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
            transition: width 0.5s ease-in-out;
        }
        .challenges-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .challenge-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            transition: all 0.2s;
        }
        .challenge-card:hover {
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .challenge-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .xp-badge {
            background: #e0e7ff;
            color: #4f46e5;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .challenge-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        .btn-edit { color: #4f46e5; text-decoration: none; font-weight: 500; }
        .btn-delete { color: #ef4444; background: none; border: none; cursor: pointer; font-weight: 500; }
    </style>

    <div class="header-section">
        <div>
            <h1>🏆 Class Leaderboard</h1>
            <p style="color: #64748b;">Motivate your students through XP and milestones!</p>
        </div>
        <a href="{{ route('teacher.gamification.create') }}" class="btn-create">
            <i class="fas fa-plus"></i> Create Challenge
        </a>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 12px; margin-bottom: 20px; border-left: 5px solid #22c55e;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <h2>🏅 Top Performers</h2>
        @if($students->count())
            <div class="leaderboard-grid">
                @foreach($students->take(3) as $index => $student)
                    <div class="top-performer">
                        <div class="avatar-container">
                            @if($index == 0) <span class="crown">👑</span> @endif
                            <img src="{{ asset('images/' . ($student->profile_pic ?? 'default-pp.png')) }}" alt="avatar">
                        </div>
                        <h3 style="font-weight: 700; color: #1e293b; margin-bottom: 5px;">{{ $student->name }}</h3>
                        <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 10px;">Level {{ $student->level }}</p>
                        <div style="font-weight: 700; color: #4f46e5;">{{ $student->points_sum_value ?? 0 }} XP</div>
                        <div class="xp-bar">
                            @php $progress = min(($student->points_sum_value ?? 0) % 200 / 2, 100); @endphp
                            <div class="xp-progress" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Full Leaderboard List -->
            <div style="background: #f8fafc; border-radius: 12px; padding: 10px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; color: #64748b; font-size: 0.85rem;">
                            <th style="padding: 12px;">Rank</th>
                            <th style="padding: 12px;">Student</th>
                            <th style="padding: 12px; text-align: center;">XP</th>
                            <th style="padding: 12px; text-align: right;">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $student)
                            <tr style="border-top: 1px solid #e2e8f0;">
                                <td style="padding: 12px; font-weight: 700; color: #4f46e5;">
                                    @if($index == 0) 🥇 @elseif($index == 1) 🥈 @elseif($index == 2) 🥉 @else {{ $index + 1 }} @endif
                                </td>
                                <td style="padding: 12px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <img src="{{ asset('images/' . ($student->profile_pic ?? 'default-pp.png')) }}" style="width: 32px; height: 32px; border-radius: 50%;">
                                        <span style="font-weight: 600;">{{ $student->name }}</span>
                                    </div>
                                </td>
                                <td style="padding: 12px; text-align: center; font-weight: 700;">{{ $student->points_sum_value ?? 0 }}</td>
                                <td style="padding: 12px; width: 150px;">
                                    @php $progress = min(($student->points_sum_value ?? 0) % 200 / 2, 100); @endphp
                                    <div class="xp-bar" style="height: 6px;">
                                        <div class="xp-progress" style="width: {{ $progress }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: #64748b; padding: 40px;">No students enrolled yet.</p>
        @endif
    </div>

    <div class="card">
        <h2>🎯 Active Challenges</h2>
        @if($challenges->count())
            <div class="challenges-grid">
                @foreach($challenges as $challenge)
                    <div class="challenge-card">
                        <div class="challenge-header">
                            <h4 style="font-weight: 700; color: #1e293b;">{{ $challenge->title }}</h4>
                            <span class="xp-badge">+{{ $challenge->points }} XP</span>
                        </div>
                        <p style="font-size: 0.85rem; color: #64748b; line-height: 1.5;">{{ $challenge->description }}</p>
                        <div class="challenge-actions">
                            <a href="{{ route('teacher.gamification.edit', $challenge->id) }}" class="btn-edit">✏️ Edit</a>
                            <button wire:click="deleteChallenge({{ $challenge->id }})" 
                                onclick="return confirm('Are you sure you want to delete this challenge?')"
                                class="btn-delete">🗑 Delete</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="text-align: center; color: #64748b; padding: 40px;">No active challenges. Add one to boost engagement!</p>
        @endif
    </div>
</div>
