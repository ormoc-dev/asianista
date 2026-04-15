<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'used',
        'is_approved',
        'approved_at',
        'first_name',
        'last_name',
        'middle_name',
        'username',
        'default_password',
        'student_code',
        'character',
        'gender',
        'user_id',
    ];

    protected $casts = [
        'used' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user associated with this registration code.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get full name of the student.
     */
    public function getFullNameAttribute()
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        return $name;
    }
}
