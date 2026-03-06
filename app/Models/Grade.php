<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
    ];

    /**
     * A grade has many sections.
     */
    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}
