<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to Google's OAuth consent screen.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'email', 'profile'])
            ->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function handleGoogleCallback(SupabaseService $supabase)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::error('Google OAuth callback failed', ['error' => $e->getMessage()]);
            return redirect('/login')->withErrors([
                'email' => 'Google sign-in failed. Please try again.',
            ]);
        }

        $googleId    = $googleUser->getId();
        $email       = strtolower(trim($googleUser->getEmail() ?? ''));
        $fullName    = $googleUser->getName() ?? '';
        $emailVerified = (bool) ($googleUser->user['email_verified'] ?? false);

        // Guard: no email from Google → bail out
        if ($email === '') {
            return redirect('/login')->withErrors([
                'email' => 'Google did not share an email address. Please use another sign-in method.',
            ]);
        }

        // Guard: Google email must be verified before we trust it
        if (!$emailVerified) {
            Log::warning('Google email not verified', ['email' => $email]);
            return redirect('/login')->withErrors([
                'email' => 'Your Google email is not verified. Please verify it with Google first.',
            ]);
        }

        // 1. Already linked to a Google account → log in
        $existingByGoogle = $supabase->findProfileByGoogleId($googleId);
        if ($existingByGoogle) {
            if (!$existingByGoogle['is_active']) {
                return redirect('/login')->withErrors([
                    'email' => 'This account has been disabled.',
                ]);
            }

            $this->setSession($existingByGoogle);
            return $this->redirectBasedOnRole($existingByGoogle['role']);
        }

        // 2. Email matches an existing password account → link
        $existingByEmail = $supabase->findProfileByEmail($email);
        if ($existingByEmail) {
            if (!$existingByEmail['is_active']) {
                return redirect('/login')->withErrors([
                    'email' => 'This account has been disabled.',
                ]);
            }

            $supabase->linkGoogleAccount($existingByEmail['id'], $googleId);
            Log::info('Google account linked to existing profile', [
                'profile_id' => $existingByEmail['id'],
                'email'      => $email,
            ]);

            $this->setSession($existingByEmail);
            return $this->redirectBasedOnRole($existingByEmail['role']);
        }

        // 3. Brand new user → create a Google-only profile
        $created = $supabase->createGoogleProfile([
            'email'         => $email,
            'password_hash' => null,
            'google_id'     => $googleId,
            'full_name'     => $fullName ?: explode('@', $email)[0],
            'role'          => 'customer',
            'is_active'     => true,
        ]);

        if (!$created) {
            return redirect('/login')->withErrors([
                'email' => 'Could not create your account. Please try again.',
            ]);
        }

        Log::info('Google-only profile created', ['email' => $email]);
        $this->setSession($created);
        return redirect('/my-orders')->with('success', 'Welcome to Oreo Bites!');
    }

    private function setSession(array $profile): void
    {
        Session::put('auth_user', [
            'id'        => $profile['id'],
            'email'     => $profile['email'],
            'full_name' => $profile['full_name'],
            'role'      => $profile['role'],
        ]);
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