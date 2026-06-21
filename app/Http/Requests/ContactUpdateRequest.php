<?php

namespace App\Http\Requests;

use App\Constants\StatusesConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'statusId' => [
                'sometimes',
                'required',
                'integer',
                Rule::in([
                    StatusesConstants::CONTACT_REQUEST,
                    StatusesConstants::CONTACT_ANSWERED,
                ]),
            ],
            'response' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'statusId.required' => 'El estado es requerido',
            'statusId.integer' => 'El estado debe ser un número entero',
            'statusId.in' => 'El estado seleccionado no es válido',
            'response.string' => 'La respuesta debe ser una cadena de caracteres',
            'response.max' => 'La respuesta debe tener un máximo de 2000 caracteres',
        ];
    }
}
