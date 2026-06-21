<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'message' => ['required', 'string', 'max:2000'],
            'companyId' => ['nullable', 'integer', 'exists:companies,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser una cadena de caracteres',
            'name.max' => 'El nombre debe tener un máximo de 255 caracteres',
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico no es válido',
            'email.max' => 'El correo electrónico debe tener un máximo de 255 caracteres',
            'phone.string' => 'El teléfono debe ser una cadena de caracteres',
            'phone.max' => 'El teléfono debe tener un máximo de 20 caracteres',
            'message.required' => 'El mensaje es requerido',
            'message.string' => 'El mensaje debe ser una cadena de caracteres',
            'message.max' => 'El mensaje debe tener un máximo de 2000 caracteres',
            'companyId.integer' => 'El identificador de empresa no es válido',
            'companyId.exists' => 'La empresa indicada no existe',
        ];
    }
}
