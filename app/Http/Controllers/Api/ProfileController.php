<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * @OA\get(
     *     path="/api/user",
     *     summary="Get User Details",
     *     tags={"Profile"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User fetched successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="user@gmail.com"),
     *                 @OA\Property(property="phone", type="string", nullable=true, example="+8801712345678"),
     *                 @OA\Property(property="profile_photo_url", type="string", nullable=true, example="avatar.jpg"),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="is_admin", type="boolean", example=false),
     *                 @OA\Property(property="is_property_owner", type="boolean", example=true),
     *                 @OA\Property(property="is_property_inspector", type="boolean", example=true),
     *                 @OA\Property(property="email_verified_at", type="string", example="2025-06-06 12:00:00"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error."),
     * )
     */
    public function user()
    {
        return $this->sendResponse(Auth::user(), 'User fetched successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/profile/change-password",
     *     summary="Change User Password",
     *     tags={"Profile"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"password", "password_confirmation"},
     *             @OA\Property(property="password", type="string", format="password", example="pa$$word"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="pa$$word")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password changed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password changed successfully")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error."),
     *     @OA\Response(response=500, description="Server error."),
     * )
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required'
        ]);

        $user = Auth::user();
        $user->password = bcrypt($request->password);
        $user->save();
        return $this->sendResponse(null, 'Password changed successfully');
    }
}
