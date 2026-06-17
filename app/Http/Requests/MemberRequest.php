<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;

class MemberRequest extends UserRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'roleId' => RoleEnum::MEMBER->value,
        ]);
    }
}
