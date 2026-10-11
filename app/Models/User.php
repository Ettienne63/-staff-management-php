<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public const SchoolRoles = ['principal', 'deputy', 'hr', 'coach', 'student'];

    public const LeadershipRoles = ['principal', 'deputy'];

    public const SingleSchoolRoles = ['principal', 'deputy', 'student'];

    public const FamilySpaceRestrictedRoles = ['principal', 'deputy', 'coach'];

    public const StaffRoles = ['principal', 'deputy', 'hr'];

    public const ManagementRoles = ['principal', 'deputy', 'hr'];

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class)->withPivot('role')->withTimestamps();
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'guardian_student')
            ->withPivot(['verified_by', 'verified_at', 'revoked_at'])->withTimestamps()->wherePivotNull('revoked_at');
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function hasMatchingChildAtAnotherSchool(int $schoolId, string $firstName, string $lastName, string $dateOfBirth): bool
    {
        return $this->children()->where('students.school_id', '!=', $schoolId)
            ->whereRaw('LOWER(first_name) = ?', [strtolower($firstName)])
            ->whereRaw('LOWER(last_name) = ?', [strtolower($lastName)])
            ->whereDate('date_of_birth', $dateOfBirth)->exists();
    }

    public function isPlatformAdmin(): bool
    {
        $email = config('app.platform_admin_email');

        return is_string($email) && trim($email) !== ''
            && $this->email === trim($email);
    }

    public function usesFamilyPortal(): bool
    {
        return ! $this->isPlatformAdmin()
            && ! $this->schools()->wherePivotIn('role', self::SchoolRoles)->exists();
    }

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }
}
