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

class AuthController extends Controller {

    public function register(RegisterRequest $request): JsonResponse {
        $data = $request->validated();
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
            'rolle'    => $data['rolle'] ?? 'Student',
        ]);
        Auth::login($user);
        $request->session()->regenerate();
        return response()->json(['message' => 'Account created.', 'user' => new UserResource($user)], 201);
    }

    public function login(LoginRequest $request): JsonResponse {
        if (!Auth::attempt($request->only('email','password'), remember: false))
            throw ValidationException::withMessages(['email' => ['Die Anmeldedaten sind nicht korrekt.']]);
        $request->session()->regenerate();
        return response()->json(['message' => 'Eingeloggt.', 'user' => new UserResource(Auth::user())]);
    }

    public function logout(Request $request): JsonResponse {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Abgemeldet.']);
    }

    public function me(Request $request): JsonResponse {
        return response()->json(['user' => new UserResource($request->user())]);
    }
}
