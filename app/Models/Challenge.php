<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'points', 'description', 'grade_id', 'section_id', 'teacher_id'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

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
    public function scopeOwnedByTeacher($query, int $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }
}
