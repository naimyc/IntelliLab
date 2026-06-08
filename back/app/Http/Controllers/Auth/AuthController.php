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
     * Legt einen neuen User an und startet direkt eine Session.
     * Konsistent mit login() — beide nutzen Session-Cookies, keinen Token.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        Auth::login($user);
        $request->session()->regenerate(); // Session-ID rotieren → kein Session-Fixation-Angriff

        return response()->json([
            'message' => 'Account created successfully.',
            'user'    => new UserResource($user),
        ], 201);
    }

    /**
     * POST /api/auth/login
     *
     * Prüft Credentials und startet eine Session.
     * Bei Fehler: generische Meldung → kein User-Enumeration möglich.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, remember: false)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $request->session()->regenerate(); // Session-ID rotieren nach erfolgreichem Login

        return response()->json([
            'message' => 'Logged in successfully.',
            'user'    => new UserResource(Auth::user()),
        ]);
    }

    /**
     * POST /api/auth/logout
     *
     * Zerstört die Session und rotiert den CSRF-Token.
     * Erfordert auth:sanctum Middleware.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken(); // Neuen CSRF-Token generieren

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * GET /api/auth/me
     *
     * Gibt den aktuell eingeloggten User zurück.
     * 401 kommt automatisch von Sanctum wenn kein gültiger Cookie.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }
}