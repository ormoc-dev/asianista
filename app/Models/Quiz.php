<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'quizzes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'direction',
        'file_path',
        'type',          // quiz, pre-test, post-test
        'status',        // pending, active, rejected
        'assign_date',
        'due_date',
        'teacher_id',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'assign_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    /**
     * Relationship: a quiz belongs to a teacher (user)
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Relationship: a quiz has many questions
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Accessor: Get file name only (for displaying)
     */
    public function getFileNameAttribute()
    {
        return $this->file_path ? basename($this->file_path) : null;
    }

    /**
     * Scope: Only active quizzes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Only currently open quizzes (based on date)
     */
    public function scopeOpen($query)
    {
        return $query->where('assign_date', '<=', now())
                     ->where('due_date', '>=', now());
    }
}
