<?php

namespace App\Enums;

enum RoleEnum: int
{
    case ADMIN = 1;
    case RECEPTIONIST = 2;
    case TRAINING = 3;
    case MEMBER = 4;

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrador',
            self::RECEPTIONIST => 'Recepcionista',
            self::TRAINING => 'Entrenador',
            self::MEMBER => 'Miembro',
        };
    }

    public function toArray(): array
    {
        return [
            'id' => $this->value,
            'name' => $this->label(),
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
