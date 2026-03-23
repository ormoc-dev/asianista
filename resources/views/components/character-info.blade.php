@props(['characterClass' => null, 'showDetails' => true])

@php
$characterData = [
    'mage' => [
        'name' => 'Mage',
        'title' => 'Master of Knowledge',
        'theme' => 'Intelligence, strategy, and solving complex problems',
        'hp' => 30,
        'ap' => 50,
        'color' => '#6c5ce7',
        'image' => 'mage.png',
        'abilities' => [
            'Spell of Insight' => 'Reveals a hint for difficult quiz questions',
            'Mana Boost' => 'Grants additional XP for correctly answering difficult questions',
            'Time Warp' => 'Provides extra time during timed quizzes or challenges',
            'Knowledge Burst' => 'Unlocks bonus questions that award extra points',
            'Arcane Analysis' => 'Eliminates one incorrect option in multiple-choice questions',
        ],
        'best_for' => 'Students who enjoy critical thinking and problem-solving',
    ],
    'warrior' => [
        'name' => 'Warrior',
        'title' => 'Champion of Action',
        'theme' => 'Strength, persistence, and completing challenges',
        'hp' => 80,
        'ap' => 30,
        'color' => '#e17055',
        'image' => 'warrior.png',
        'abilities' => [
            'Power Strike' => 'Doubles the points for the next correct answer',
            'Streak Master' => 'Grants bonus XP for consecutive correct answers',
            'Shield Guard' => 'Prevents point loss after one incorrect answer',
            'Battle Rush' => 'Allows answering two questions quickly with bonus XP',
            'Challenge Duel' => 'Enables challenging another player for additional points',
        ],
        'best_for' => 'Students who enjoy competition and fast-paced gameplay',
    ],
    'healer' => [
        'name' => 'Healer',
        'title' => 'Support of the Team',
        'theme' => 'Collaboration, helping others, and consistency',
        'hp' => 50,
        'ap' => 35,
        'color' => '#00b894',
        'image' => 'healer.png',
        'abilities' => [
            'Healing Light' => 'Restores lost XP after an incorrect answer',
            'Team Blessing' => 'Provides a small XP bonus to teammates in group activities',
            'Revive' => 'Allows retrying a question without penalty',
            'Focus Aura' => 'Reduces mistakes by allowing a second attempt',
            'Wisdom Share' => 'Earns XP by assisting classmates in discussions or tasks',
        ],
        'best_for' => 'Students who enjoy collaboration and helping others',
    ],
];

$character = $characterClass ? ($characterData[$characterClass] ?? null) : null;
@endphp

@if($character)
    <div class="character-info" style="border: 2px solid {{ $character['color'] }}; border-radius: 12px; padding: 20px; background: linear-gradient(135deg, {{ $character['color'] }}15 0%, {{ $character['color'] }}05 100%);">
        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 16px;">
            <img src="{{ asset('images/' . $character['image']) }}" alt="{{ $character['name'] }}" style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid {{ $character['color'] }};">
            <div>
                <h3 style="margin: 0; color: {{ $character['color'] }}; font-size: 1.5rem;">{{ $character['name'] }}</h3>
                <p style="margin: 4px 0 0 0; font-style: italic; color: var(--text-muted);">{{ $character['title'] }}</p>
            </div>
        </div>

        <div style="margin-bottom: 16px;">
            <p style="margin: 0; color: var(--text-muted);"><strong>Theme:</strong> {{ $character['theme'] }}</p>
        </div>

        <div style="display: flex; gap: 20px; margin-bottom: 16px;">
            <div style="text-align: center; padding: 12px 20px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 1.5rem; font-weight: bold; color: #e74c3c;">{{ $character['hp'] }}</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">HP (Health)</div>
            </div>
            <div style="text-align: center; padding: 12px 20px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 1.5rem; font-weight: bold; color: #3498db;">{{ $character['ap'] }}</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">AP (Action)</div>
            </div>
        </div>

        @if($showDetails)
            <div style="margin-bottom: 16px;">
                <h4 style="margin: 0 0 12px 0; font-size: 1.1rem;">Abilities</h4>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($character['abilities'] as $ability => $description)
                        <li style="margin-bottom: 8px;">
                            <strong>{{ $ability }}:</strong> {{ $description }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div style="padding: 12px; background: {{ $character['color'] }}20; border-radius: 8px; border-left: 4px solid {{ $character['color'] }};">
                <strong>Best suited for:</strong> {{ $character['best_for'] }}
            </div>
        @endif
    </div>
@else
    <div style="text-align: center; padding: 40px; color: var(--text-muted);">
        <p>Select a character class to see details</p>
    </div>
@endif
