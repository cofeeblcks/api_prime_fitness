<?php

namespace App\Enums;

enum HandEnum: int
{
    case LEFT = 1;
    case RIGHT = 2;

    public function label(): string
    {
        return match($this) {
            self::LEFT => 'Mano Izquierda',
            self::RIGHT => 'Mano Derecha',
        };
    }
}
