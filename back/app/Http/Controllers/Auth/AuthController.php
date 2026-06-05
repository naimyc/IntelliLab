<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/auth/register
     *
     * Creates a new user account and starts an authenticated session.
     * Password is auto-hashed by the User model's 'hashed' cast.
     */
public function register(RegisterRequest $request): JsonResponse
{
    $user = User::create($request->validated());

    $token = $user->createToken('api-token')->plainTextToken;

    

    return response()->json([
        'message' => 'Account created successfully.',
        'user' => new UserResource($user),
        'token' => $token,
    ], 201);
}

    /**
     * POST /api/auth/login
     *
     * Validates credentials and establishes a session.
     * Throws 422 on bad credentials (never reveal which field is wrong).
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, remember: false)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $request->session()->regenerate(); // prevent session fixation

        return response()->json([
            'message' => 'Logged in successfully.',
            'user'    => new UserResource(Auth::user()),
        ]);
    }

    /**
     * POST /api/auth/logout
     *
     * Destroys the current session and invalidates the CSRF token.
     * Requires auth:sanctum middleware.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken(); // rotate CSRF token

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * GET /api/auth/me
     *
     * Returns the currently authenticated user.
     * Returns 401 automatically if not authenticated (Sanctum middleware).
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }
}