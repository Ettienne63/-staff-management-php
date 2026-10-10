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
        abort_if($request->user()->isPlatformAdmin(), 404);

        $school = $request->route('school');
        if (! $school instanceof School) {
            $school = School::findOrFail($school);
        }
        $membership = $request->user()->schools()->whereKey($school->id)
            ->wherePivotIn('role', User::SchoolRoles)->first();
        abort_unless($membership, 404);
        $singleSchool = $request->user()->schools()->wherePivotIn('role', User::SingleSchoolRoles)->orderBy('schools.id')->first();
        abort_if($singleSchool && $singleSchool->getKey() !== $school->getKey(), 404);
        $request->attributes->set('school_role', $membership->pivot->role);

        return $next($request);
    }
}
