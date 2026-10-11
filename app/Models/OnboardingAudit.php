<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['school_id', 'student_id', 'actor_id', 'action', 'context'])]
class OnboardingAudit extends Model
{
    protected function casts(): array
    {
        return ['context' => 'array'];
    }
}
