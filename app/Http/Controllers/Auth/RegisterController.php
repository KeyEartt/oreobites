<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function register(Request $request, SupabaseService $supabase)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'password'  => [
                'required', 'string', 'min:8', 'confirmed',
                'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/',
            ],
        ], [
            'password.regex' => 'Password must include uppercase, lowercase, a number, and a symbol.',
        ]);

        $existing = $supabase->findProfileByEmail($validated['email']);
        if ($existing) {
            return back()->withErrors(['email' => 'This email is already registered.'])->withInput();
        }

        $created = $supabase->createProfile([
            'email'         => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'full_name'     => $validated['full_name'],
            'role'          => 'customer',
            'is_active'     => true,
        ]);

        if (!$created) {
            return back()->withErrors(['email' => 'Registration failed. Please try again.'])->withInput();
        }

        Session::put('auth_user', [
            'id'        => $created['id'],
            'email'     => $created['email'],
            'full_name' => $created['full_name'],
            'role'      => $created['role'],
        ]);

        // Same intent-redirect logic
        $intended = Session::pull('url.intended');
        if ($intended) {
            return redirect($intended)->with('success', 'Welcome to Oreo Bites!');
        }

        return redirect('/my-orders')->with('success', 'Welcome to Oreo Bites!');
    }
}