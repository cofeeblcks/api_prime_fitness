<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccessControlRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'identification' => ['required', 'string', 'max:255'],
            'date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'identification.required' => 'La identificación es requerida',
            'identification.string' => 'La identificación debe ser una cadena de caracteres',
            'identification.max' => 'La identificación debe tener un máximo de 255 caracteres',
            'date.date' => 'La fecha debe tener un formato válido',
        ];
    }
}
