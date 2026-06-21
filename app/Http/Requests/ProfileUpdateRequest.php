<?php

namespace App\Http\Requests;

use App\Enums\SexEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => ['required', 'string', 'max:13'],
            'birthdate' => ['required', 'date'],
            'sex' => ['required', Rule::enum(SexEnum::class)],
            'identification' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'identification')->ignore($userId),
            ],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:'.config('filesystems.file_size')],
            'height' => ['nullable', 'numeric', 'min:0.50', 'max:2.50'],
            'password' => ['nullable', 'string', 'min:6'],
            'identificationTypeId' => ['required', 'numeric', 'exists:identification_types,id'],
        ];
    }
}
