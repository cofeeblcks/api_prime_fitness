<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeightControlRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'weight' => ['required', 'numeric', 'min:1', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'weight.required' => 'El peso es requerido',
            'weight.numeric' => 'El peso debe ser un número',
            'weight.min' => 'El peso debe ser mayor a 0',
            'weight.max' => 'El peso no puede superar 500 kg',
        ];
    }
}
