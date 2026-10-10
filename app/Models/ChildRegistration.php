<?php

namespace App\Models;

use Database\Factories\ChildRegistrationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['school_id', 'guardian_user_id', 'first_name', 'last_name', 'date_of_birth', 'grade', 'relationship', 'student_email', 'status', 'student_id', 'reviewed_by', 'reviewed_at', 'review_note'])]
class ChildRegistration extends Model
{
    /** @use HasFactory<ChildRegistrationFactory> */
    use HasFactory;

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guardian_user_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    protected function casts(): array
    {
        return ['date_of_birth' => 'date', 'reviewed_at' => 'datetime', 'grade' => 'integer'];
    }
}
