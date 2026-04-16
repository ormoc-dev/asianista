<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'character',
        'gender',
        'profile_pic',
        'status',
        'hp',
        'ap',
        'xp',
        'level',
        'first_name',
        'last_name',
        'middle_name',
        'grade_id',
        'section_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    // 🔥 Messaging relationships
    public function conversations()
    {
        return $this->belongsToMany(\App\Models\Conversation::class)
                    ->withPivot('last_read_at', 'deleted_at')
                    ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(\App\Models\Message::class);
    }

    public function questAttempts()
    {
        return $this->hasMany(\App\Models\QuestAttempt::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(\App\Models\QuizAttempt::class, 'student_id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function isOnline(): bool
    {
        if (!$this->last_seen_at) {
            return false;
        }

        // online if active in last 5 minutes
        return $this->last_seen_at->gt(now()->subMinutes(5));
    }

    /**
     * Character class constants with HP/AP stats.
     */
    const CHARACTER_CLASSES = [
        'mage' => [
            'name' => 'Mage',
            'title' => 'Master of Knowledge',
            'theme' => 'Intelligence, strategy, and solving complex problems',
            'hp' => 30,
            'ap' => 50,
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

    /**
     * Initialize character stats based on selected class.
     *
     * @param string $characterClass
     * @return void
     */
    public function initializeCharacterStats(string $characterClass): void
    {
        $classData = self::CHARACTER_CLASSES[$characterClass] ?? null;

        if ($classData) {
            $this->hp = $classData['hp'];
            $this->ap = $classData['ap'];
            $this->character = $characterClass;
        }
    }

    /**
     * Get character class data.
     *
     * @return array|null
     */
    public function getCharacterData(): ?array
    {
        return self::CHARACTER_CLASSES[$this->character] ?? null;
    }

    /**
     * Get full name of the user.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        return $name;
    }
}
