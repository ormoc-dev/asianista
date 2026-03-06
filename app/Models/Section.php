<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'grade_id',
        'name',
    ];

    /**
     * A section belongs to a grade.
     */
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}
