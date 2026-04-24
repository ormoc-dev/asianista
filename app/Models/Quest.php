<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'difficulty', 'map_image', 'map_pins', 'level', 'xp_reward', 'ab_reward', 'gp_reward',
        'time_limit_minutes', 'hp_penalty',
        'assign_date', 'due_date', 'grade_id', 'section_id', 'teacher_id'
    ];

    protected $casts = [
        'map_pins' => 'array',
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

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopeOwnedByTeacher($query, int $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Same rules as Quiz::isVisibleToStudent: untargeted quests (no grade/section) are visible
     * to all students; targeted quests require the student's roster grade and section to match.
     */
    public function isVisibleToStudent(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($this->grade_id === null && $this->section_id === null) {
            return true;
        }

        if (! $user->grade_id || ! $user->section_id) {
            return false;
        }

        return (int) $this->grade_id === (int) $user->grade_id
            && (int) $this->section_id === (int) $user->section_id;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopeVisibleToStudent($query, ?User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where(function ($inner) {
                $inner->whereNull('grade_id')->whereNull('section_id');
            });
            if ($user && $user->grade_id && $user->section_id) {
                $q->orWhere(function ($inner) use ($user) {
                    $inner->where('grade_id', $user->grade_id)
                        ->where('section_id', $user->section_id);
                });
            }
        });
    }
}
