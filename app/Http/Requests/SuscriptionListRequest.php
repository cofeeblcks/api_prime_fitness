<?php

namespace App\Http\Requests;

class SuscriptionListRequest extends PaginationRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'status_id' => ['nullable', 'integer', 'exists:statuses,id'],
            'search' => ['nullable', 'string'],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'user_id.integer' => 'El usuario debe ser un número entero.',
            'user_id.exists' => 'El usuario seleccionado no es válido.',
            'plan_id.integer' => 'El plan debe ser un número entero.',
            'plan_id.exists' => 'El plan seleccionado no es válido.',
            'status_id.integer' => 'El estado debe ser un número entero.',
            'status_id.exists' => 'El estado seleccionado no es válido.',
            'search.string' => 'El campo de búsqueda debe ser una cadena de texto.',
        ]);
    }
}
