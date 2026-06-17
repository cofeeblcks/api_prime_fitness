<?php

namespace App\Enums;

enum IntakeEnum: int
{
    case BREAKFAST = 1;
    case MORNING_SNACK = 2;
    case LUNCH = 3;
    case AFTERNOON_SNACK = 4;
    case DINNER = 5;
    case EVENING_SNACK = 6;

    public function label(): string
    {
        return match($this) {
            self::BREAKFAST => 'Desayuno',
            self::MORNING_SNACK => 'Snack Mañana',
            self::LUNCH => 'Almuerzo',
            self::AFTERNOON_SNACK => 'Snack Tarde',
            self::DINNER => 'Cena',
            self::EVENING_SNACK => 'Snack Noche',
        };
    }
}
