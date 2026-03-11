<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'quest_id', 'current_question_id', 'status', 'score'
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
}
