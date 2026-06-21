<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
{
    public function rules(): array
    {
        $isUpdate = in_array($this->getMethod(), ['PUT', 'PATCH']);

        return [
            'name' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'slogan' => ['nullable', 'string', 'max:255'],
            'logo' => [
                $isUpdate ? 'nullable' : 'required',
                'image',
                'mimes:jpeg,png,jpg,gif,svg,webp',
                'max:'.config('filesystems.file_size'),
            ],
            'address' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'description' => [$isUpdate ? 'sometimes' : 'required', 'string'],
            'links' => ['nullable', 'array'],
            'links.*.id' => ['nullable', 'integer', 'exists:company_links,id'],
            'links.*.username' => ['required_with:links', 'string', 'max:255'],
            'links.*.link' => ['required_with:links', 'string', 'url', 'max:255'],
            'links.*.linkTypeId' => ['required_with:links', 'integer', 'exists:link_types,id'],
            'emails' => ['nullable', 'array'],
            'emails.*.id' => ['nullable', 'integer', 'exists:company_emails,id'],
            'emails.*.email' => ['required_with:emails', 'string', 'email', 'max:255'],
            'phones' => ['nullable', 'array'],
            'phones.*.id' => ['nullable', 'integer', 'exists:company_phones,id'],
            'phones.*.phone' => ['required_with:phones', 'string', 'max:20'],
            'services' => ['nullable', 'array'],
            'services.*.id' => ['nullable', 'integer', 'exists:company_services,id'],
            'services.*.description' => ['required_with:services', 'string'],
            'coordinates' => ['nullable', 'array'],
            'coordinates.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'coordinates.longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser una cadena de caracteres',
            'name.max' => 'El nombre debe tener un máximo de 255 caracteres',
            'slogan.string' => 'El eslogan debe ser una cadena de caracteres',
            'slogan.max' => 'El eslogan debe tener un máximo de 255 caracteres',
            'logo.required' => 'El logo es requerido',
            'logo.image' => 'El logo debe ser una imagen',
            'logo.mimes' => 'El logo debe ser un archivo de imagen válido',
            'logo.max' => 'El logo debe tener un tamaño máximo de '.config('filesystems.file_size'),
            'address.required' => 'La dirección es requerida',
            'address.string' => 'La dirección debe ser una cadena de caracteres',
            'address.max' => 'La dirección debe tener un máximo de 255 caracteres',
            'description.required' => 'La descripción es requerida',
            'description.string' => 'La descripción debe ser una cadena de caracteres',
            'links.array' => 'Los enlaces deben ser un arreglo',
            'links.*.id.integer' => 'El identificador del enlace debe ser un número entero',
            'links.*.id.exists' => 'El enlace seleccionado no es válido',
            'links.*.username.required_with' => 'El usuario del enlace es requerido',
            'links.*.username.string' => 'El usuario del enlace debe ser una cadena de caracteres',
            'links.*.username.max' => 'El usuario del enlace debe tener un máximo de 255 caracteres',
            'links.*.link.required_with' => 'La URL del enlace es requerida',
            'links.*.link.string' => 'La URL del enlace debe ser una cadena de caracteres',
            'links.*.link.url' => 'La URL del enlace debe ser válida',
            'links.*.link.max' => 'La URL del enlace debe tener un máximo de 255 caracteres',
            'links.*.linkTypeId.required_with' => 'El tipo de enlace es requerido',
            'links.*.linkTypeId.integer' => 'El tipo de enlace debe ser un número entero',
            'links.*.linkTypeId.exists' => 'El tipo de enlace no existe',
            'emails.array' => 'Los correos deben ser un arreglo',
            'emails.*.id.integer' => 'El identificador del correo debe ser un número entero',
            'emails.*.id.exists' => 'El correo seleccionado no es válido',
            'emails.*.email.required_with' => 'El correo es requerido',
            'emails.*.email.string' => 'El correo debe ser una cadena de caracteres',
            'emails.*.email.email' => 'El correo debe ser un correo electrónico válido',
            'emails.*.email.max' => 'El correo debe tener un máximo de 255 caracteres',
            'phones.array' => 'Los teléfonos deben ser un arreglo',
            'phones.*.id.integer' => 'El identificador del teléfono debe ser un número entero',
            'phones.*.id.exists' => 'El teléfono seleccionado no es válido',
            'phones.*.phone.required_with' => 'El teléfono es requerido',
            'phones.*.phone.string' => 'El teléfono debe ser una cadena de caracteres',
            'phones.*.phone.max' => 'El teléfono debe tener un máximo de 20 caracteres',
            'services.array' => 'Los servicios deben ser un arreglo',
            'services.*.id.integer' => 'El identificador del servicio debe ser un número entero',
            'services.*.id.exists' => 'El servicio seleccionado no es válido',
            'services.*.description.required_with' => 'La descripción del servicio es requerida',
            'services.*.description.string' => 'La descripción del servicio debe ser una cadena de caracteres',
            'coordinates.array' => 'Las coordenadas deben ser un objeto',
            'coordinates.latitude.numeric' => 'La latitud debe ser un número',
            'coordinates.latitude.between' => 'La latitud debe estar entre -90 y 90',
            'coordinates.longitude.numeric' => 'La longitud debe ser un número',
            'coordinates.longitude.between' => 'La longitud debe estar entre -180 y 180',
        ];
    }
}
