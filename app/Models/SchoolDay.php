<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'title',
        'type',
        'description',
        'attendance_rate',
        'is_school_open',
    ];

    protected $casts = [
        'date' => 'date',
        'attendance_rate' => 'decimal:2',
        'is_school_open' => 'boolean',
    ];
}
