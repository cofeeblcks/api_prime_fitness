<?php

namespace App\Http\Requests;

class PlanListRequest extends PaginationRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'search' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'search.string' => 'El campo de búsqueda debe ser una cadena de texto.',
            'is_active.boolean' => 'El campo activo debe ser verdadero o falso.',
        ]);
    }
}
