<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // open registration
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'], // needs password_confirmation field
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'         => 'An account with this email already exists.',
            'password.confirmed'   => 'Password confirmation does not match.',
            'password.min'         => 'Password must be at least 8 characters.',
        ];
    }
}