<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\User;
use App\Utils\PasswordResetUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    public function __construct(protected PasswordResetUtils $passwordResetUtils)
    {
        $this->passwordResetUtils = $passwordResetUtils;
    }

    /**
     * @OA\Post(
     *     path="/api/auth/forget-password/otp/send",
     *     summary="Send OTP for password reset",
     *     description="You can use 123456 OTP for testing.",
     *     tags={"Forget Password"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", example="test@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP sent successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or password same as current.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="OTP has already been sent.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Too many failed attempts. You are suspended for 10 minutes.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error."
     *     )
     * )
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $otpRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if ($otpRecord) {
            if (! $this->passwordResetUtils->isExpired($otpRecord)) {
                return $this->sendError('OTP has already been sent.', null, 400);
            }

            $this->passwordResetUtils->deleteResetToken($request->email);
        }

        $otp = $this->passwordResetUtils->generateOtp();

        $otpRecord = DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if (in_array(env('APP_ENV'), ['local', 'staging'])) {
            return $this->sendResponse(null, 'OTP sent successfully.');
        }

        $user = User::where('email', $request->email)->first();
        Mail::to($request->email)->send(new PasswordResetOtpMail($otp, $user));

        return $this->sendResponse(null, 'OTP sent successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/auth/forget-password/otp/resend",
     *     summary="Resend OTP for password reset",
     *     description="You can use 123456 OTP for testing.",
     *     tags={"Forget Password"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", example="test@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP sent successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="OTP record not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="OTP record not found."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or password same as current.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many failed attempts.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Too many failed attempts.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error."
     *     )
     * )
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:password_reset_tokens,email'
        ]);

        $email = $request->email;

        $otpRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$otpRecord) {
            return $this->sendError('OTP record not found.', null, 404);
        }

        if ($this->passwordResetUtils->isSuspended($otpRecord)) {
            return $this->sendError('You are suspended due to too many OTP requests.', null, 429);
        }

        if ($this->passwordResetUtils->isRateLimited($otpRecord)) {
            return $this->sendError('You must wait 1 minute before resending the OTP.', null, 429);
        }

        $updateData = ['updated_at' => now()];

        if ($this->passwordResetUtils->isExpired($otpRecord)) {
            $updateData['resent_count'] = 1;
            $updateData['failed_attempts'] = 0;
        } elseif ($otpRecord->resent_count >= 3) {
            if ($otpRecord->suspend_duration && now()->greaterThan($otpRecord->suspend_duration)) {
                $updateData['resent_count'] = 1;
                $updateData['failed_attempts'] = 0;
                $updateData['suspend_duration'] = null;
            } else {
                $updateData['suspend_duration'] = now()->addMinutes(10);
                DB::table('password_reset_tokens')
                    ->where('email', $email)
                    ->update($updateData);

                return $this->sendError('You are suspended due to too many OTP requests.', null, 429);
            }
        } else {
            $updateData['resent_count'] = $otpRecord->resent_count + 1;
        }

        $otp = $this->passwordResetUtils->generateOtp();

        $updateData += [
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
            'is_verified' => false,
        ];

        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->update($updateData);

        if (in_array(env('APP_ENV'), ['local', 'staging'])) {
            return $this->sendResponse([], 'OTP sent successfully.');
        }

        $user = User::where('email', $email)->first();
        Mail::to($request->email)->send(new PasswordResetOtpMail($otp, $user));

        return $this->sendResponse([], 'OTP sent successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/auth/forget-password/otp/verify",
     *     summary="Verify OTP and optionally reset password",
     *     description="Verifies the OTP and optionally resets the password. Use OTP 123456 for testing.",
     *     tags={"Forget Password"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"otp", "password", "password_confirmation"},
     *             @OA\Property(property="otp", type="string", example="123456"),
     *             @OA\Property(property="password", type="string", minLength=8, example="NewPassword@123"),
     *             @OA\Property(property="password_confirmation", type="string", example="NewPassword@123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified and password reset successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP verified and password reset successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid OTP or expired token.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid OTP.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="OTP not verified.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="OTP not verified. Please verify your OTP first.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="OTP or user not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=410,
     *         description="OTP has expired.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="OTP has expired.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or password same as current.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many failed attempts.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Too many failed attempts. You are suspended for 10 minutes.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error."
     *     )
     * )
     */
    public function verifyAndReset(Request $request): JsonResponse
    {
        $request->validate([
            'otp' => 'required|digits:6|exists:password_reset_tokens,otp',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                // 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            ],
            'password_confirmation' => 'required_with:password'
        ], [
            'password.regex' => 'The password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        $otp = $request->otp;
        $password = $request->password;

        $otpRecord = DB::table('password_reset_tokens')
            ->where('otp', $otp)
            ->first();

        if (!$otpRecord) {
            return $this->sendError('OTP not found.', null, 404);
        }

        if ($this->passwordResetUtils->isExpired($otpRecord)) {
            $this->passwordResetUtils->deleteResetToken($otpRecord->email);
            return $this->sendError('OTP has expired.', null, 410);
        }

        if (isset($otpRecord->suspend_duration) && now()->lessThan($otpRecord->suspend_duration)) {
            return $this->sendError('Too many failed attempts. Please try again later.', null, 429);
        }

        if ($otpRecord->otp != $otp) {
            $failedAttempts = $otpRecord->failed_attempts + 1;

            DB::table('password_reset_tokens')
                ->where('otp', $otp)
                ->update([
                    'failed_attempts' => $failedAttempts,
                    'suspend_duration' => $failedAttempts >= 3 ? now()->addMinutes(10) : null
                ]);

            if ($failedAttempts >= 3) {
                return $this->sendError('Too many failed attempts. You are suspended for 10 minutes.', null, 429);
            }

            return $this->sendError('Invalid OTP.', null, 401);
        }

        DB::table('password_reset_tokens')
            ->where('otp', $otp)
            ->update([
                'is_verified' => true,
                'failed_attempts' => 0,
                'suspend_duration' => null
            ]);

        if (!$password) {
            return $this->sendResponse(null, 'OTP verified successfully.');
        }

        $user = User::where('email', $otpRecord->email)->first();
        if (!$user) {
            return $this->sendError('User not found.', null, 404);
        }

        if (Hash::check($password, $user->password)) {
            return $this->sendError('New password cannot be the same as the current password.', null, 422);
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->passwordResetUtils->deleteResetToken($otpRecord->email);

        return $this->sendResponse(
            null,
            'OTP verified and password reset successfully.',
        );
    }
}
