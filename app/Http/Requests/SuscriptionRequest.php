<?php

namespace App\Http\Requests;

use App\Constants\StatusesConstants;
use App\Constants\StatusTypesConstants;
use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SuscriptionRequest extends FormRequest
{
    public function rules(): array
    {
        $isUpdate = in_array($this->getMethod(), ['PUT', 'PATCH']);

        $subscriptionStatusIds = [
            StatusesConstants::SUBSCRIPTION_ACTIVE,
            StatusesConstants::SUBSCRIPTION_SUSPENDED,
            StatusesConstants::SUBSCRIPTION_CANCELLED,
        ];

        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('suscriptions', 'code')->ignore($this->route('suscription'))->whereNull('deleted_at'),
            ],
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after_or_equal:startDate'],
            'price' => ['required', 'numeric', 'min:0'],
            'userId' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role_id', RoleEnum::MEMBER->value),
            ],
            'planId' => ['required', 'integer', 'exists:plans,id'],
            'suscriptionTypeId' => ['required', 'integer', 'exists:suscription_types,id'],
            'statusId' => [
                'required',
                'integer',
                Rule::exists('statuses', 'id')->where('status_type_id', StatusTypesConstants::SUBSCRIPTION),
                Rule::in($subscriptionStatusIds),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'El código es requerido',
            'code.string' => 'El código debe ser una cadena de caracteres',
            'code.max' => 'El código debe tener un máximo de 255 caracteres',
            'code.unique' => 'El código ya se encuentra registrado',
            'startDate.required' => 'La fecha de inicio es requerida',
            'startDate.date' => 'La fecha de inicio debe tener un formato válido',
            'endDate.required' => 'La fecha de fin es requerida',
            'endDate.date' => 'La fecha de fin debe tener un formato válido',
            'endDate.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio',
            'price.required' => 'El precio es requerido',
            'price.numeric' => 'El precio debe ser un número',
            'price.min' => 'El precio debe ser mayor o igual a 0',
            'userId.required' => 'El miembro es requerido',
            'userId.integer' => 'El miembro debe ser un número entero',
            'userId.exists' => 'El miembro seleccionado no es válido',
            'planId.required' => 'El plan es requerido',
            'planId.integer' => 'El plan debe ser un número entero',
            'planId.exists' => 'El plan seleccionado no es válido',
            'suscriptionTypeId.required' => 'El tipo de membresía es requerido',
            'suscriptionTypeId.integer' => 'El tipo de membresía debe ser un número entero',
            'suscriptionTypeId.exists' => 'El tipo de membresía seleccionado no es válido',
            'statusId.required' => 'El estado es requerido',
            'statusId.integer' => 'El estado debe ser un número entero',
            'statusId.exists' => 'El estado seleccionado no es válido',
            'statusId.in' => 'El estado de membresía no es válido',
        ];
    }
}
