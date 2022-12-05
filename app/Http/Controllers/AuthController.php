<?php
/*
 * Controller Used For User Authentications Actions
 * Login - Register - Logout
 */
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * This function is used for user Login and return token.
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Either Email / Password is not valid',
                'code' => 401
            ], 401);
        }

        return response()->json([
            'message' => 'Logged in successfully',
            'token' => $user->createToken('Basic')->plainTextToken,
            'user_id' => $user->id,
        ], 200);
    }

    /**
     * This function is used for user registration and return Bearer Access token.
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            return response()->json([
                'message' => 'User Already Exists, Please Try Logging In',
                'code' => 400
            ], 400);
        }

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $user->save();
        if (!$user) {
            return response()->json([
                'message' => 'Something Went Wrong, Please Try Again.',
                'code' => 400
            ], 400);
        }
        return response()->json([
            'message' => 'Registered successfully',
            'token' => $user->createToken('Basic')->plainTextToken,
            'user_id' => $user->id,
        ], 200);
    }

    /**
     * This function is used for user logout and delete the token used for authentication.
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse {
        if (Auth::check()) {
            Auth::user()->currentAccessToken()->delete();
            return response()->json([
                'message' => 'Logged out successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Something Went Wrong, Please Try Again.',
                'code' => 400
            ], 400);
        }
    }
}
