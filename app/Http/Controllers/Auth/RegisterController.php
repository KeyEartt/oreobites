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
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if email exists
        $existing = $supabase->findProfileByEmail($validated['email']);
        if ($existing) {
            return back()->withErrors(['email' => 'This email is already registered.'])->withInput();
        }

        // Create profile with customer role
        $created = $supabase->createProfile([
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'full_name' => $validated['full_name'],
            'role' => 'customer',
            'is_active' => true,
        ]);

        if (!$created) {
            return back()->withErrors(['email' => 'Registration failed. Please try again.'])->withInput();
        }

        // Auto-login
        Session::put('auth_user', [
            'id' => $created['id'],
            'email' => $created['email'],
            'full_name' => $created['full_name'],
            'role' => $created['role'],
        ]);

        return redirect('/my-orders')->with('success', 'Welcome to Oreo Bites!');
    }
}