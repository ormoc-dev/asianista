<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'file_path',
        'teacher_id',
        'status',
        'section',
        'grade_id',
        'section_id',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function sectionModel()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    /**
     * Same idea as Quiz/Quest: untargeted lessons (no grade/section) stay visible to everyone.
     * Targeted lessons match the student's roster grade_id and section_id.
     * Legacy rows may only have the "Grade - Section" string in `section`; those match by label.
     */
    public function isVisibleToStudent(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($this->grade_id !== null && $this->section_id !== null
            && $user->grade_id && $user->section_id) {
            return (int) $this->grade_id === (int) $user->grade_id
                && (int) $this->section_id === (int) $user->section_id;
        }

        if ($this->section && $user->grade_id && $user->section_id) {
            $user->loadMissing(['grade', 'section']);
            if ($user->grade && $user->section) {
                $label = $user->grade->name.' - '.$user->section->name;

                return $this->section === $label;
            }
        }

        if ($this->grade_id === null && $this->section_id === null
            && (! $this->section || $this->section === '')) {
            return true;
        }

        return false;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopeVisibleToStudent($query, ?User $user)
    {
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        $user->loadMissing(['grade', 'section']);
        $label = ($user->grade && $user->section)
            ? ($user->grade->name.' - '.$user->section->name)
            : null;

        return $query->where(function ($q) use ($user, $label) {
            $q->where(function ($inner) {
                $inner->whereNull('grade_id')->whereNull('section_id')
                    ->where(function ($s) {
                        $s->whereNull('section')->orWhere('section', '');
                    });
            });
            if ($user->grade_id && $user->section_id) {
                $q->orWhere(function ($inner) use ($user) {
                    $inner->where('grade_id', $user->grade_id)
                        ->where('section_id', $user->section_id);
                });
            }
            if ($label) {
                $q->orWhere('section', $label);
            }
        });
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
