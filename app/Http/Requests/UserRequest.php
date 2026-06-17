<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use App\Enums\SexEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function rules(): array
    {
        $isUpdate = in_array($this->getMethod() , ['PUT', 'PATCH']);

        return [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => [
                Rule::requiredIf(!$this->user()), 'nullable', 'string', 'email', 'max:255', $isUpdate ?
                    'exists:users,email' :
                    'unique:users,email,NULL,id,deleted_at,NULL'
            ],
            'phone' => ['required', 'string', 'max:13'],
            'birthdate' => ['required', 'date'],
            'sex' => ['required', 'in:'.implode(',', SexEnum::cases())],
            'identification' => [
                'required', 'string', 'max:255', $isUpdate ?
                'exists:users,identification' :
                'unique:users,identification,NULL,id,deleted_at,NULL'
            ],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:'.config('filesystems.file_size')],
            'height' => ['nullable', 'numeric', 'min:0.50', 'max:2.50'],
            'password' => [Rule::requiredIf(!$this->user() && !$isUpdate), 'nullable', 'string', 'min:6'],
            'roleId' => ['required', 'numeric', 'exists:roles,id'],
            'identificationTypeId' => ['required', 'numeric', 'exists:identification_types,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'firstName.required' => 'El nombre es requerido',
            'firstName.string' => 'El nombre debe ser una cadena de caracteres',
            'firstName.max' => 'El nombre debe tener un máximo de 255 caracteres',
            'lastName.required' => 'El apellido es requerido',
            'lastName.string' => 'El apellido debe ser una cadena de caracteres',
            'lastName.max' => 'El apellido debe tener un máximo de 255 caracteres',
            'email.required' => 'El correo es requerido',
            'email.string' => 'El correo debe ser una cadena de caracteres',
            'email.email' => 'El correo debe ser un correo electrónico válido',
            'email.max' => 'El correo debe tener un máximo de 255 caracteres',
            'email.unique' => 'El correo ya se encuentra registrado',
            'phone.required' => 'El teléfono es requerido',
            'phone.string' => 'El teléfono debe ser una cadena de caracteres',
            'phone.max' => 'El teléfono debe tener un máximo de 13 caracteres',
            'birthdate.required' => 'La fecha de nacimiento es requerida',
            'birthdate.date' => 'La fecha de nacimiento debe tener un formato válido',
            'sex.required' => 'El sexo es requerido',
            'sex.in' => 'El sexo debe ser un valor válido',
            'identification.required' => 'La identificación es requerida',
            'identification.string' => 'La identificación debe ser una cadena de caracteres',
            'identification.max' => 'La identificación debe tener un máximo de 255 caracteres',
            'identification.unique' => 'La identificación ya se encuentra registrada',
            'photo.image' => 'La foto debe ser una imagen',
            'photo.mimes' => 'La foto debe ser un archivo de imagen válido',
            'photo.max' => 'La foto debe tener un tamaño máximo de '.config('filesystems.file_size'),
            'height.numeric' => 'La altura debe ser un número',
            'height.min' => 'La altura debe ser al menos 0.50',
            'height.max' => 'La altura debe ser como máximo 2.50',
            'password.required' => 'La contraseña es requerida',
            'password.string' => 'La contraseña debe ser una cadena de caracteres',
            'password.min' => 'La contraseña debe tener un mínimo de 6 caracteres',
            'roleId.required' => 'El rol es requerido',
            'roleId.numeric' => 'El rol debe ser un número',
            'roleId.exists' => 'El rol no existe',
            'identificationTypeId.required' => 'El tipo de identificación es requerido',
            'identificationTypeId.numeric' => 'El tipo de identificación debe ser un número',
            'identificationTypeId.exists' => 'El tipo de identificación no existe',
        ];
    }
}
