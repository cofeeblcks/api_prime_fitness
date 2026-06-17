<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;

class TrainerRequest extends UserRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'roleId' => RoleEnum::TRAINING->value,
        ]);
    }
}
