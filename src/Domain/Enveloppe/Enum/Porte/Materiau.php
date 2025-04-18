<?php

namespace App\Domain\Enveloppe\Enum\Porte;

use App\Domain\Common\Enum\Enum;

enum Materiau: string implements Enum
{
    case PVC = 'pvc';
    case BOIS = 'bois';
    case BOIS_METAL = 'bois_metal';
    case METAL = 'metal';

    public static function from_enum_type_porte_id(int $type_porte_id): self
    {
        return match ($type_porte_id) {
            1, 2, 3, 4 => self::BOIS,
            5, 6, 7, 8 => self::PVC,
            9, 10, 11, 12 => self::METAL,
            default => null,
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
        };
    }
}
