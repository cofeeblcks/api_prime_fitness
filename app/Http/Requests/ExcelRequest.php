<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExcelRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:' . config('filesystems.file_size')],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'El documento es requerido.',
            'file.file' => 'El documento debe ser un archivo valido.',
            'file.mimes' => 'El documento debe ser un archivo .xlsx o .xls.',
        ];
    }
}
