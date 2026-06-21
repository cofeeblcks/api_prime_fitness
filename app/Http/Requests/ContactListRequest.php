<?php

namespace App\Http\Requests;

class ContactListRequest extends PaginationRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'status_id' => ['nullable', 'integer', 'exists:statuses,id'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'search' => ['nullable', 'string'],
            'startDate' => ['nullable', 'date'],
            'endDate' => ['nullable', 'date', 'after_or_equal:startDate'],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'status_id.integer' => 'El estado debe ser un número entero.',
            'status_id.exists' => 'El estado seleccionado no es válido.',
            'company_id.integer' => 'La empresa debe ser un número entero.',
            'company_id.exists' => 'La empresa seleccionada no es válida.',
            'search.string' => 'El campo de búsqueda debe ser una cadena de texto.',
            'startDate.date' => 'La fecha de inicio debe tener un formato válido.',
            'endDate.date' => 'La fecha de fin debe tener un formato válido.',
            'endDate.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
        ]);
    }
}
