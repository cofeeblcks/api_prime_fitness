<?php

namespace App\Http\Requests;

class AccessControlListRequest extends PaginationRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'status_id' => ['nullable', 'integer', 'exists:statuses,id'],
            'search' => ['nullable', 'string'],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'status_id.integer' => 'El estado debe ser un número entero.',
            'status_id.exists' => 'El estado seleccionado no es válido.',
            'search.string' => 'El campo de búsqueda debe ser una cadena de texto.',
        ]);
    }
}
