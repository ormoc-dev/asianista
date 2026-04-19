<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentFeedback extends Model
{
    /** Laravel would infer `student_feedback` (singular); migration uses `student_feedbacks`. */
    protected $table = 'student_feedbacks';

    protected $fillable = [
        'teacher_id',
        'student_id',
        'type',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }
}
