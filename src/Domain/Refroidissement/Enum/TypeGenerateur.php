<?php

namespace App\Domain\Refroidissement\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeGenerateur: string implements Enum
{
    case PAC_AIR_AIR = 'PAC_AIR_AIR';
    case PAC_AIR_EAU = 'PAC_AIR_EAU';
    case PAC_EAU_EAU = 'PAC_EAU_EAU';
    case PAC_EAU_GLYCOLEE_EAU = 'PAC_EAU_GLYCOLEE_EAU';
    case PAC_GEOTHERMIQUE = 'PAC_GEOTHERMIQUE';
    case RESEAU_FROID = 'RESEAU_FROID';
    case AUTRE_SYSTEME_THERMODYNAMIQUE = 'AUTRE_SYSTEME_THERMODYNAMIQUE';
    case AUTRE = 'AUTRE';

    public static function from_enum_type_generateur_fr_id(int $id): self
    {
        return match ($id) {
            1, 2, 3 => self::PAC_AIR_AIR,
            4, 5, 6, 7 => self::PAC_AIR_EAU,
            8, 9, 10, 11 => self::PAC_EAU_EAU,
            12, 13, 14, 15 => self::PAC_EAU_GLYCOLEE_EAU,
            16, 17, 18, 19 => self::PAC_GEOTHERMIQUE,
            20 => self::AUTRE_SYSTEME_THERMODYNAMIQUE,
            21 => self::AUTRE_SYSTEME_THERMODYNAMIQUE,
            22 => self::AUTRE,
            23 => self::RESEAU_FROID,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::PAC_AIR_AIR => 'PAC air/air',
            self::PAC_AIR_EAU => 'PAC air/eau',
            self::PAC_EAU_EAU => 'PAC eau/eau',
            self::PAC_EAU_GLYCOLEE_EAU => 'PAC eau glycolée/eau',
            self::PAC_GEOTHERMIQUE => 'PAC géothermique',
            self::AUTRE_SYSTEME_THERMODYNAMIQUE => 'Système thermodynamique',
            self::AUTRE => 'Système de refroidissement',
            self::RESEAU_FROID => 'Réseau de froid urbain'
        };
    }

    public function seer_applicable(): bool
    {
        return \in_array($this, [
            self::PAC_AIR_AIR,
            self::PAC_AIR_EAU,
            self::PAC_EAU_EAU,
            self::PAC_EAU_GLYCOLEE_EAU,
            self::PAC_GEOTHERMIQUE,
            self::AUTRE_SYSTEME_THERMODYNAMIQUE,
        ]);
    }
}
