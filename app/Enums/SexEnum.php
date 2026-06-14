<?php

namespace App\Enums;

enum SexEnum: int
{
    case MALE = 1;
    case FEMALE = 2;
    
    public function label(): string
    {
        return match($this) {
            self::MALE => 'Masculino',
            self::FEMALE => 'Femenino',
        };
    }
}
