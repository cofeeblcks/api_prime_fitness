<?php

namespace App\Http\Requests;

class UserIdRequest extends PaginationRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'user_id.integer' => 'El ID del usuario debe ser un número entero.',
            'user_id.exists' => 'El usuario seleccionado no existe.',
        ]);
    }
}
