<?php

namespace App\Http\Middleware;

use App\Models\School;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchoolAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $school = $request->route('school');
        if (! $school instanceof School) {
            $school = School::findOrFail($school);
        }
        $membership = $request->user()->schools()->whereKey($school->id)
            ->wherePivotIn('role', User::SchoolRoles)->first();
        abort_unless($membership, 404);
        $leadershipSchool = $request->user()->schools()->wherePivotIn('role', User::LeadershipRoles)->orderBy('schools.id')->first();
        abort_if($leadershipSchool && $leadershipSchool->getKey() !== $school->getKey(), 404);
        $request->attributes->set('school_role', $membership->pivot->role);

        return $next($request);
    }
}
