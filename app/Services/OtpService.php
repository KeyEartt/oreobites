<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    public const TTL_MINUTES             = 10;
    public const MAX_ATTEMPTS            = 5;
    public const RESEND_COOLDOWN_SECONDS = 60;

    public function generate(string $email): string
    {
        DB::table('password_reset_otps')->where('email', $email)->delete();

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_otps')->insert([
            'email'      => $email,
            'otp_hash'   => Hash::make($otp),
            'attempts'   => 0,
            'expires_at' => now()->addMinutes(self::TTL_MINUTES),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $otp;
    }

    public function verify(string $email, string $otp): bool
    {
        $row = DB::table('password_reset_otps')->where('email', $email)->first();

        if (! $row) {
            return false;
        }

        if (now()->greaterThan($row->expires_at)) {
            DB::table('password_reset_otps')->where('email', $email)->delete();
            return false;
        }

        if ($row->attempts >= self::MAX_ATTEMPTS) {
            return false;
        }

        if (! Hash::check($otp, $row->otp_hash)) {
            DB::table('password_reset_otps')
                ->where('email', $email)
                ->increment('attempts', 1, ['updated_at' => now()]);
            return false;
        }

        return true;
    }

    public function consume(string $email): void
    {
        DB::table('password_reset_otps')->where('email', $email)->delete();
    }

    public function resendCooldownRemaining(string $email): int
    {
        $row = DB::table('password_reset_otps')->where('email', $email)->first();

        if (! $row) {
            return 0;
        }

        $createdAt   = \Carbon\Carbon::parse($row->created_at);
        $availableAt = $createdAt->copy()->addSeconds(self::RESEND_COOLDOWN_SECONDS);

        return max(0, (int) now()->diffInSeconds($availableAt, false));
    }
}