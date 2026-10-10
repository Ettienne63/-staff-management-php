<?php

namespace App\Models;

use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['school_id', 'user_id', 'first_name', 'last_name', 'date_of_birth', 'grade'])]
class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory;

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'guardian_student')
            ->withPivot(['verified_by', 'verified_at', 'revoked_at'])->withTimestamps();
    }

    protected function casts(): array
    {
        return ['date_of_birth' => 'date', 'grade' => 'integer'];
    }
}
