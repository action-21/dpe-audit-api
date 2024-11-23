<?php

namespace App\Domain\Porte\Enum;

use App\Domain\Common\Enum\Enum;

enum NatureMenuiserie: string implements Enum
{
    case INCONNU = 'INCONNU';
    case PVC = 'PVC';
    case BOIS = 'BOIS';
    case BOIS_METAL = 'BOIS_METAL';
    case METAL = 'METAL';

    public static function from_enum_type_porte_id(int $type_porte_id): self
    {
        return match ($type_porte_id) {
            1, 2, 3, 4 => self::BOIS,
            5, 6, 7, 8 => self::PVC,
            9, 10, 11, 12 => self::METAL,
            13, 14, 15 => self::INCONNU,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::PVC => 'PVC',
            self::BOIS => 'Bois',
            self::BOIS_METAL => 'Bois et Métal',
            self::METAL => 'Métal',
            self::INCONNU => 'Inconnu',
        };
    }
}
