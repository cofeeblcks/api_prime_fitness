<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanRequest extends FormRequest
{
    public function rules(): array
    {
        $isUpdate = in_array($this->getMethod(), ['PUT', 'PATCH']);

        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('plans', 'code')->ignore($this->route('plan'))->whereNull('deleted_at'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'isActive' => ['nullable', 'boolean'],
            'details' => ['nullable', 'array'],
            'details.*.id' => ['nullable', 'integer', 'exists:plan_details,id'],
            'details.*.item' => ['required_with:details', 'string', 'max:255'],
            'details.*.isActive' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'El código es requerido',
            'code.string' => 'El código debe ser una cadena de caracteres',
            'code.max' => 'El código debe tener un máximo de 255 caracteres',
            'code.exists' => 'El código ya se encuentra registrado',
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser una cadena de caracteres',
            'name.max' => 'El nombre debe tener un máximo de 255 caracteres',
            'description.required' => 'La descripción es requerida',
            'description.string' => 'La descripción debe ser una cadena de caracteres',
            'price.required' => 'El precio es requerido',
            'price.numeric' => 'El precio debe ser un número',
            'price.min' => 'El precio debe ser mayor o igual a 0',
            'isActive.boolean' => 'El campo activo debe ser verdadero o falso',
            'details.array' => 'Los detalles deben ser un arreglo',
            'details.*.id.integer' => 'El identificador del detalle debe ser un número entero',
            'details.*.id.exists' => 'El detalle seleccionado no es válido',
            'details.*.item.required_with' => 'El ítem del detalle es requerido',
            'details.*.item.string' => 'El ítem del detalle debe ser una cadena de caracteres',
            'details.*.item.max' => 'El ítem del detalle debe tener un máximo de 255 caracteres',
            'details.*.isActive.boolean' => 'El campo activo del detalle debe ser verdadero o falso',
        ];
    }
}
