<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quest_id',
        'current_question_id',
        'status',
        'score',
        'question_outcomes',
        'completed_at',
    ];

    protected $casts = [
        'question_outcomes' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quest()
    {
        return $this->belongsTo(Quest::class);
    }

    public function currentQuestion()
    {
        return $this->belongsTo(QuestQuestion::class, 'current_question_id');
    }

    public function usedPowers()
    {
        return $this->hasMany(QuestAttemptPower::class, 'quest_attempt_id');
    }

    public function hasUsedPower($powerName, $level)
    {
        return $this->usedPowers()
            ->where('power_name', $powerName)
            ->where('level', $level)
            ->exists();
    }

    public function usePower($powerName, $level)
    {
        // Caller (e.g. StudentQuestController) already validates; avoid a duplicate exists() query.
        return $this->usedPowers()->create([
            'power_name' => $powerName,
            'level' => $level,
        ]);
    }
}
