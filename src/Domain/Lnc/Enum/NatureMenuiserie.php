<?php

namespace App\Domain\Lnc\Enum;

use App\Domain\Common\Enum\Enum;

enum NatureMenuiserie: int implements Enum
{
    case POLYCARBONATE = 1;
    case BOIS = 2;
    case BOIS_METAL = 3;
    case PVC = 4;
    case METAL_AVEC_RUPTEUR_PONT_THERMIQUE = 5;
    case METAL_SANS_RUPTEUR_PONT_THERMIQUE = 6;

    public static function scope(): string
    {
        return 'local non chauffé . baie . nature de la menuiserie';
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::POLYCARBONATE => 'Polycarbonate',
            self::BOIS => 'Bois',
            self::BOIS_METAL => 'Bois/métal',
            self::PVC => 'PVC',
            self::METAL_AVEC_RUPTEUR_PONT_THERMIQUE => 'Métal avec rupture de pont thermique',
            self::METAL_SANS_RUPTEUR_PONT_THERMIQUE => 'Métal sans rupture de pont thermique'
        };
    }

    public function type_vitrage_requis(): bool
    {
        return $this !== self::POLYCARBONATE;
    }
}
