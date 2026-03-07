<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'difficulty', 'level', 'xp_reward', 'ab_reward', 'gp_reward',
        'assign_date', 'due_date', 'grade_id', 'section_id', 'teacher_id'
    ];

    public function questions()
    {
        return $this->hasMany(QuestQuestion::class);
    }
}
