<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Services\OtpService;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;

class ForgotPasswordController extends Controller
{
    private const SESSION_EMAIL    = 'password_reset_email';
    private const SESSION_VERIFIED = 'password_reset_verified_at';
    private const VERIFY_TTL_MIN   = 15;

    // ---------- STEP 1: request form ----------

    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request, SupabaseService $supabase, OtpService $otp)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower(trim($validated['email']));

        // Rate limit: 3 requests per 15 min per email
        $key = 'otp-request:' . $email;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Too many reset attempts. Try again in {$seconds} seconds.",
            ])->withInput();
        }
        RateLimiter::hit($key, 900);

        // Cooldown from OtpService (prevents rapid re-request per email)
        $cooldown = $otp->resendCooldownRemaining($email);
        if ($cooldown > 0) {
            return back()->withErrors([
                'email' => "Please wait {$cooldown}s before requesting a new code.",
            ])->withInput();
        }

        $profile = $supabase->findProfileByEmail($email);

        // Always show the same success message whether or not the email exists
        // (prevents account enumeration). Only send if the account exists.
        if ($profile && $profile['is_active']) {
            $code = $otp->generate($email);

            try {
                Mail::to($email)->send(new OtpMail(
                    otp:           $code,
                    recipientName: explode(' ', $profile['full_name'])[0],
                ));
            } catch (\Throwable $e) {
                Log::error('OTP mail failed', ['email' => $email, 'error' => $e->getMessage()]);
                return back()->withErrors([
                    'email' => 'We could not send the reset email. Please try again.',
                ])->withInput();
            }
        }

        Session::put(self::SESSION_EMAIL, $email);
        Session::forget(self::SESSION_VERIFIED);

        return redirect('/forgot-password/verify')
            ->with('status', 'If that email is registered, a 6-digit code is on its way.');
    }

    // ---------- STEP 2: verify OTP ----------

    public function showVerifyForm()
    {
        if (! Session::has(self::SESSION_EMAIL)) {
            return redirect('/forgot-password');
        }

        return view('auth.verify-otp', [
            'email' => Session::get(self::SESSION_EMAIL),
        ]);
    }

    public function verifyOtp(Request $request, OtpService $otp)
    {
        $email = Session::get(self::SESSION_EMAIL);

        if (! $email) {
            return redirect('/forgot-password')
                ->withErrors(['email' => 'Session expired. Please start again.']);
        }

        $validated = $request->validate([
            'otp' => 'required|digits:6',
        ]);

        if (! $otp->verify($email, $validated['otp'])) {
            return back()->withErrors([
                'otp' => 'Invalid or expired code. Try again or request a new one.',
            ]);
        }

        Session::put(self::SESSION_VERIFIED, now()->timestamp);

        return redirect('/forgot-password/reset');
    }

    // ---------- STEP 3: set new password ----------

    public function showResetForm()
    {
        if (! $this->isVerified()) {
            return redirect('/forgot-password');
        }

        return view('auth.reset-password');
    }

    public function resetPassword(Request $request, SupabaseService $supabase, OtpService $otp)
    {
        $email = Session::get(self::SESSION_EMAIL);

        if (! $email || ! $this->isVerified()) {
            return redirect('/forgot-password')
                ->withErrors(['email' => 'Session expired. Please start again.']);
        }

        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],
        ], [
            'password.regex' => 'Password must include uppercase, lowercase, a number, and a symbol.',
        ]);

        $profile = $supabase->findProfileByEmail($email);

        if (! $profile) {
            return redirect('/forgot-password')
                ->withErrors(['email' => 'Account no longer exists.']);
        }

        $updated = $supabase->updateProfile($profile['id'], [
            'password_hash' => Hash::make($validated['password']),
            'updated_at'    => now()->utc()->format('Y-m-d\TH:i:s\Z'),
        ]);

        if (! $updated) {
            return back()->withErrors([
                'password' => 'Could not update your password. Please try again.',
            ]);
        }

        $otp->consume($email);

        Session::forget([self::SESSION_EMAIL, self::SESSION_VERIFIED]);

        return redirect('/login')->with('success', 'Password reset. Please log in.');
    }

    // ---------- Helpers ----------

    private function isVerified(): bool
    {
        $ts = Session::get(self::SESSION_VERIFIED);
        if (! $ts) {
            return false;
        }

        $ageMinutes = (now()->timestamp - $ts) / 60;
        return $ageMinutes <= self::VERIFY_TTL_MIN;
    }
}