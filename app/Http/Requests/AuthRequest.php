<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'El correo electrónico es requerido.',
            'email.string' => 'El correo electrónico debe ser un texto.',
            'email.email' => 'El correo electrónico debe ser un correo electrónico válido.',
            'email.exists' => 'El correo electrónico no existe.',
            'password.required' => 'La contraseña es requerida.',
            'password.string' => 'La contraseña debe ser un texto.',
            'password.min' => 'La contraseña debe tener un mínimo de 6 caracteres.',
        ];
    }
}
