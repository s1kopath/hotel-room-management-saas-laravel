<?php

namespace App\Utils;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PasswordResetUtils
{
    const OTP_LENGTH = 6;
    const OTP_EXPIRY_MINUTES = 5;
    const MAX_ATTEMPTS = 3;
    const SUSPENSION_MINUTES = 10;
    const RESEND_DELAY_SECONDS = 60;

    public static function generateOtp(): string
    {
        return in_array(env('APP_ENV'), ['local', 'staging'])
            ? env('TEST_OTP', 123456)
            : rand(100000, 999999);
    }

    public function isRateLimited($otpRecord)
    {
        return Carbon::parse($otpRecord->updated_at)->diffInSeconds(now()) < self::RESEND_DELAY_SECONDS;
    }

    public function isSuspended($otpRecord): bool
    {
        return $otpRecord->suspend_duration && now()->lt($otpRecord->suspend_duration);
    }

    public function isExpired($otpRecord): bool
    {
        return now()->gt($otpRecord->expires_at);
    }

    public function createPasswordResetToken($email): string
    {
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => now(),
                'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES)
            ]
        );

        return $token;
    }

    public function validateResetToken($email, $token): bool
    {
        return DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->exists();
    }

    public function deleteResetToken($email): void
    {
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();
    }

    public function getRemainingAttempts($email): int
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        return $record ? max(0, self::MAX_ATTEMPTS - $record->failed_attempts) : self::MAX_ATTEMPTS;
    }
}
