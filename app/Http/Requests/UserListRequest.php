<?php

namespace App\Http\Requests;

class UserListRequest extends PaginationRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
            'search' => ['nullable', 'string'],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'roles.array' => 'El campo rol debe ser un array.',
            'roles.*.integer' => 'El campo rol debe ser un número entero.',
            'roles.*.exists' => 'El rol seleccionado no es válido.',
            'search.string' => 'El campo de búsqueda debe ser una cadena de texto.',
        ]);
    }
}
