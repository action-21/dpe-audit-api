<?php

namespace App\Domain\Enveloppe\Enum\Baie;

use App\Domain\Common\Enum\Enum;

enum Materiau: string implements Enum
{
    case BOIS = 'bois';
    case BOIS_METAL = 'bois_metal';
    case PVC = 'pvc';
    case METAL = 'metal';

    public static function from_enum_type_materiaux_menuiserie_id(int $id): ?self
    {
        return match ($id) {
            1, 2 => null,
            3 => self::BOIS,
            4 => self::BOIS_METAL,
            5 => self::PVC,
            6 => self::METAL,
            7 => self::METAL,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::BOIS => 'Bois',
            self::BOIS_METAL => 'Bois/métal',
            self::PVC => 'PVC',
            self::METAL => 'Métal',
        };
    }
}
