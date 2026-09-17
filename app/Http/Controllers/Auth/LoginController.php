<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $profile = $supabase->findProfileByEmail($validated['email']);

        if (!$profile) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        if (!$profile['is_active']) {
            return back()->withErrors(['email' => 'Account is disabled.'])->withInput();
        }

        if (!Hash::check($validated['password'], $profile['password_hash'])) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        // Store user in session (never store password_hash)
        Session::put('auth_user', [
            'id' => $profile['id'],
            'email' => $profile['email'],
            'full_name' => $profile['full_name'],
            'role' => $profile['role'],
        ]);

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
        'admin' => redirect('/admin'),
        'staff' => redirect('/staff'),
        'customer' => redirect('/my-orders'),
        default => redirect('/'),
    };
}
}