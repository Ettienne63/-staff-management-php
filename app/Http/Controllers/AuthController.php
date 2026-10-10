<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages(['email' => 'Invalid email or password.']);
        }
        $request->session()->regenerate();

        if ($request->user()->isPlatformAdmin()) {
            $request->session()->forget('url.intended');

            return redirect()->route('internal.schools.index');
        }

        $dashboard = $request->user()->usesFamilyPortal() ? 'family.dashboard' : 'dashboard';
        $student = $request->user()->student()->first();
        if ($student && $request->user()->schools()->whereKey($student->school_id)->wherePivot('role', 'student')->exists()) {
            return redirect()->intended(route('schools.workspace', $student->school_id));
        }

        return redirect()->intended(route($dashboard));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
