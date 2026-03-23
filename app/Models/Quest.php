<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'difficulty', 'map_image', 'level', 'xp_reward', 'ab_reward', 'gp_reward',
        'time_limit_minutes', 'hp_penalty',
        'assign_date', 'due_date', 'grade_id', 'section_id', 'teacher_id'
    ];

    public function questions()
    {
        return $this->hasMany(QuestQuestion::class)->orderBy('level')->orderBy('id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuestAttempt::class);
    }
}
