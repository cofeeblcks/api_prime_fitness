<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function rules(): array
    {
        $isUpdate = in_array($this->getMethod() , ['PUT', 'PATCH']);
        $isReferral = $this->input('role_id') == RoleEnum::REFERRALS->value;

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'identification' => [
                'required', 'string', 'max:255', $isUpdate ?
                'exists:users,identification' :
                'unique:users,identification,NULL,id,deleted_at,NULL'],
            'phone' => ['required', 'string', 'max:13'],
            'email' => [
                Rule::requiredIf(!$isReferral), 'nullable', 'string', 'email', 'max:255', $isUpdate ?
                    'exists:users,email' :
                    'unique:users,email,NULL,id,deleted_at,NULL'
            ],
            'password' => [Rule::requiredIf(!$isReferral && !$isUpdate), 'nullable', 'string', 'min:6'],
            'address' => ['required', 'string', 'max:255'],
            'role_id' => ['required', 'numeric', 'exists:roles,id'],
            'identification_type_id' => ['required', 'numeric', 'exists:identification_types,id'],
            'birth_date' => ['nullable', 'date'],
            'electoral_status' => ['required', 'string', 'max:255'],
            'user_id' => ['nullable', 'numeric', 'exists:users,id'],
            'observation' => ['nullable', 'string'],
            'residence' => ['required', 'array', 'min:1'],
            'residence.neighborhood' => ['required', 'string', 'max:255'],
            'residence.territory' => ['required', 'string', 'max:255'],
            'residence.locality' => ['nullable', 'string', 'max:255'],
            'residence.city_id' => ['required', 'numeric', 'exists:cities,id'],
            'polling_station' => ['required', 'array', 'min:1'],
            'polling_station.table' => ['required', 'string', 'max:255'],
            'polling_station.locus' => ['required', 'string', 'max:255'],
            'polling_station.address' => ['required', 'string', 'max:255'],
            'polling_station.neighborhood' => ['required', 'string', 'max:255'],
            'polling_station.territory' => ['required', 'string', 'max:255'],
            'polling_station.locality' => ['nullable', 'string', 'max:255'],
            'polling_station.city_id' => ['required', 'numeric', Rule::in([686])],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'El nombre es requerido',
            'first_name.string' => 'El nombre debe ser una cadena de caracteres',
            'first_name.max' => 'El nombre debe tener un máximo de 255 caracteres',
            'last_name.required' => 'El apellido es requerido',
            'last_name.string' => 'El apellido debe ser una cadena de caracteres',
            'last_name.max' => 'El apellido debe tener un máximo de 255 caracteres',
            'identification.required' => 'La identificación es requerida',
            'identification.string' => 'El número de identificación debe ser una cadena de caracteres',
            'identification.max' => 'El número de identificación debe tener un máximo de 255 caracteres',
            'identification.unique' => 'El número de identificación ya se encuentra registrado.',
            'phone.required' => 'El teléfono es requerido',
            'phone.string' => 'El número de teléfono debe ser una cadena de caracteres',
            'phone.max' => 'El número de teléfono debe tener un máximo de 13 caracteres',
            'email.required' => 'El correo es requerido',
            'email.string' => 'El correo debe ser una cadena de caracteres.',
            'email.email' => 'El correo debe ser un correo electrónico válido.',
            'email.max' => 'El correo debe tener un máximo de 255 caracteres.',
            'email.unique' => 'El correo ya se encuentra registrado.',
            'password.required' => 'La contraseña es requerida',
            'password.string' => 'La contraseña debe ser una cadena de caracteres.',
            'password.min' => 'La contraseña debe tener un mínimo de 6 caracteres.',
            'address.required' => 'La dirección es requerida',
            'address.string' => 'La dirección debe ser una cadena de caracteres.',
            'address.max' => 'La dirección debe tener un máximo de 255 caracteres.',
            'role_id.required' => 'El rol es requerido',
            'role_id.numeric' => 'El rol debe ser un número.',
            'role_id.exists' => 'El rol no existe.',
            'birth_date.date' => 'La fecha de nacimiento debe tener un formato válido.',
            'identification_type_id.required' => 'El tipo de identificación es requerido',
            'identification_type_id.numeric' => 'El tipo de identificación debe ser un número.',
            'identification_type_id.exists' => 'El tipo de identificación no existe.',
            'electoral_status.required' => 'El estado electoral del usuario es requerido',
            'electoral_status.string' => 'El estado electoral del usuario debe ser una cadena de caracteres.',
            'electoral_status.max' => 'El estado electoral del usuario debe tener un máximo de 255 caracteres.',
            'user_id.numeric' => 'El usuario relacionado debe ser un número.',
            'user_id.exists' => 'El usuario relacionado no existe.',
            'observation.string' => 'La observación debe ser una cadena de caracteres.',
            'residence.required' => 'La información de residencia es requerida.',
            'residence.array' => 'La residencia debe enviarse como un arreglo.',
            'residence.min' => 'Debe registrar al menos una residencia.',
            'residence.neighborhood.required' => 'El barrio de la residencia es requerido.',
            'residence.neighborhood.string' => 'El barrio de la residencia debe ser una cadena de caracteres.',
            'residence.neighborhood.max' => 'El barrio de la residencia debe tener un máximo de 255 caracteres.',
            'residence.territory.required' => 'El territorio de la residencia es requerido.',
            'residence.territory.string' => 'El territorio de la residencia debe ser una cadena de caracteres.',
            'residence.territory.max' => 'El territorio de la residencia debe tener un máximo de 255 caracteres.',
            'residence.locality.string' => 'La localidad de la residencia debe ser una cadena de caracteres.',
            'residence.locality.max' => 'La localidad de la residencia debe tener un máximo de 255 caracteres.',
            'residence.city_id.required' => 'La ciudad de la residencia es requerida.',
            'residence.city_id.numeric' => 'La ciudad de la residencia debe ser un número.',
            'residence.city_id.exists' => 'La ciudad de la residencia no existe.',
            'polling_station.required' => 'La información del puesto de votación es requerida.',
            'polling_station.array' => 'El puesto de votación debe enviarse como un arreglo.',
            'polling_station.min' => 'Debe registrar al menos un puesto de votación.',
            'polling_station.table.required' => 'La mesa de votación es requerida.',
            'polling_station.table.string' => 'La mesa de votación debe ser una cadena de caracteres.',
            'polling_station.table.max' => 'La mesa de votación debe tener un máximo de 255 caracteres.',
            'polling_station.locus.required' => 'El lugar de votación es requerido.',
            'polling_station.locus.string' => 'El lugar de votación debe ser una cadena de caracteres.',
            'polling_station.locus.max' => 'El lugar de votación debe tener un máximo de 255 caracteres.',
            'polling_station.address.required' => 'La dirección del puesto de votación es requerida.',
            'polling_station.address.string' => 'La dirección del puesto de votación debe ser una cadena de caracteres.',
            'polling_station.address.max' => 'La dirección del puesto de votación debe tener un máximo de 255 caracteres.',
            'polling_station.neighborhood.required' => 'El barrio del puesto de votación es requerido.',
            'polling_station.neighborhood.string' => 'El barrio del puesto de votación debe ser una cadena de caracteres.',
            'polling_station.neighborhood.max' => 'El barrio del puesto de votación debe tener un máximo de 255 caracteres.',
            'polling_station.territory.required' => 'El territorio del puesto de votación es requerido.',
            'polling_station.territory.string' => 'El territorio del puesto de votación debe ser una cadena de caracteres.',
            'polling_station.territory.max' => 'El territorio del puesto de votación debe tener un máximo de 255 caracteres.',
            'polling_station.locality.string' => 'La localidad del puesto de votación debe ser una cadena de caracteres.',
            'polling_station.locality.max' => 'La localidad del puesto de votación debe tener un máximo de 255 caracteres.',
            'polling_station.city_id.required' => 'La ciudad del puesto de votación es requerida.',
            'polling_station.city_id.numeric' => 'La ciudad del puesto de votación debe ser un número.',
            'polling_station.city_id.in' => 'Solo se pueden crear usuarios con puesto de votación en Barrancabermeja.',
        ];
    }
}
