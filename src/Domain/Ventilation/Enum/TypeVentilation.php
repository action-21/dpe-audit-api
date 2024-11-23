<?php

namespace App\Domain\Ventilation\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeVentilation: string implements Enum
{
    case VENTILATION_NATURELLE = 'VENTILATION_NATURELLE';
    case VENTILATION_MECANIQUE_CENTRALISEE = 'VENTILATION_MECANIQUE_CENTRALISEE';
    case VENTILATION_MECANIQUE_DIVISEE = 'VENTILATION_MECANIQUE_DIVISEE';

    public static function from_enum_type_ventilation_id(int $id): self
    {
        return match ($id) {
            1, 2, 25, 34 => self::VENTILATION_NATURELLE,
            default => self::VENTILATION_MECANIQUE_CENTRALISEE,
        };
    }

    public static function from_type_generateur(TypeGenerateur $type_generateur): self
    {
        return match ($type_generateur) {
            TypeGenerateur::VMI, TypeGenerateur::VMR => self::VENTILATION_MECANIQUE_DIVISEE,
            default => self::VENTILATION_MECANIQUE_CENTRALISEE,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::VENTILATION_NATURELLE => "Ventilation naturelle",
            self::VENTILATION_MECANIQUE_CENTRALISEE => "Ventilation mécanique centralisée",
            self::VENTILATION_MECANIQUE_DIVISEE => "Ventilation mécanique divisée",
        };
    }
}
