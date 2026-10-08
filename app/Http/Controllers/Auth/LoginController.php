<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Session::has('auth_user')) {
            return $this->redirectBasedOnRole(Session::get('auth_user')['role']);
        }
        return view('auth.login');
    }

    public function login(Request $request, SupabaseService $supabase)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $throttleKey = 'login:' . strtolower($validated['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Too many login attempts. Try again in {$seconds} seconds.",
            ])->withInput();
        }

        $profile = $supabase->findProfileByEmail($validated['email']);

        if (!$profile) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        if (!$profile['is_active']) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['email' => 'Account is disabled.'])->withInput();
        }

        if (!Hash::check($validated['password'], $profile['password_hash'])) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        RateLimiter::clear($throttleKey);

        Session::put('auth_user', [
            'id'        => $profile['id'],
            'email'     => $profile['email'],
            'full_name' => $profile['full_name'],
            'role'      => $profile['role'],
        ]);

        // If they were on their way somewhere, send them there
        $intended = Session::pull('url.intended');
        if ($intended) {
            return redirect($intended);
        }

        return $this->redirectBasedOnRole($profile['role']);
    }

    public function logout(Request $request)
    {
        Session::forget('auth_user');
        return redirect('/login');
    }

    private function redirectBasedOnRole($role)
    {
        return match ($role) {
            'admin'    => redirect('/admin'),
            'staff'    => redirect('/staff'),
            'customer' => redirect('/my-orders'),
            default    => redirect('/'),
        };
    }
}