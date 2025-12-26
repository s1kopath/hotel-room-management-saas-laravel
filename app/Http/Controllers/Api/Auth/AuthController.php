<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="User Login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="user@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="pa$$word")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User logged in successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJh..."),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="user@gmail.com"),
     *                     @OA\Property(property="phone", type="string", nullable=true, example="+8801712345678"),
     *                     @OA\Property(property="profile_photo_url", type="string", nullable=true, example="avatar.jpg"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="is_admin", type="boolean", example=false),
     *                     @OA\Property(property="is_property_owner", type="boolean", example=true),
     *                     @OA\Property(property="is_property_inspector", type="boolean", example=true),
     *                     @OA\Property(property="email_verified_at", type="string", example="2025-06-06 12:00:00"),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid credentials."),
     *     @OA\Response(response=403, description="Your account is suspended."),
     *     @OA\Response(response=404, description="User not found."),
     *     @OA\Response(response=422, description="Validation error."),
     *     @OA\Response(response=500, description="Server error."),
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->sendError('User not found.', null, 404);
        }

        if (!$user->is_active) {
            return $this->sendError('Your account is suspended.', null, 403);
        }

        if ($user->is_admin) {
            return $this->sendError('You cannot log in from this app.', null, 403);
        }

        if (! Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            return $this->sendError('Incorrect credentials.', null, 401);
        }

        $token = $user->createToken(env('APP_NAME'))->accessToken;

        return $this->sendResponse([
            'token' => $token,
            'user' => $user
        ], 'User logged in successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="User Register",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="user@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="pa$$word"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="pa$$word")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJh..."),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="user@gmail.com"),
     *                     @OA\Property(property="phone", type="string", nullable=true, example="+8801712345678"),
     *                     @OA\Property(property="profile_photo_url", type="string", nullable=true, example="avatar.jpg"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="is_admin", type="boolean", example=false),
     *                     @OA\Property(property="is_property_owner", type="boolean", example=true),
     *                     @OA\Property(property="is_property_inspector", type="boolean", example=true),
     *                     @OA\Property(property="email_verified_at", type="string", example="2025-06-06 12:00:00"),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error."),
     *     @OA\Response(response=500, description="Server error."),
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken(env('APP_NAME'))->accessToken;

        return $this->sendResponse([
            'token' => $token,
            'user' => $user
        ], 'User registered successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/logout",
     *     summary="User Logout",
     *     tags={"Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User logged out successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function logout()
    {
        $user = Auth::user();

        if ($user) {
            $user->tokens()->delete();
            return $this->sendResponse(null, 'User logged out successfully');
        }

        return $this->sendError('Unauthenticated', null, 401);
    }
}
