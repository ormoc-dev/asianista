<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'random_event_id',
        'teacher_id',
        'started_at',
        'expires_at',
        'is_active',
        'affected_students',
        'recipient_mode',
        'recipient_student_ids',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'affected_students' => 'array',
        'recipient_student_ids' => 'array',
    ];

    public function randomEvent()
    {
        return $this->belongsTo(RandomEvent::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public static function getCurrentActive()
    {
        return self::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->with('randomEvent')
            ->first();
    }

    public static function deactivateAll()
    {
        return self::where('is_active', true)->update(['is_active' => false]);
    }

    /**
     * Whether this student should receive the active random-event modal.
     */
    public function studentMayReceiveEvent(int $studentId): bool
    {
        $mode = $this->recipient_mode ?? 'all';

        if ($mode === 'all') {
            return true;
        }

        $ids = $this->recipient_student_ids ?? [];

        return in_array($studentId, $ids, true);
    }
}
