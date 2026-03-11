<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_number',
        'first_name',
        'last_name',
        'email',
        'gender',
        'date_of_birth',
        'department',
        'year_level',
        'phone_number',
        'address',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'year_level' => 'integer',
    ];

    protected $appends = [
        'full_name',
    ];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)->withTimestamps();
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(
            fn (): string => trim(sprintf('%s %s', $this->first_name, $this->last_name))
        );
    }
}
