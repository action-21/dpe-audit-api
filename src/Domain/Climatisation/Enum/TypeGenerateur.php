<?php

namespace App\Domain\Climatisation\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeGenerateur: int implements Enum
{
    case PAC_AIR_AIR = 1;
    case PAC_AIR_EAU = 2;
    case PAC_EAU_EAU = 3;
    case PAC_EAU_GLYCOLEE_EAU = 4;
    case PAC_GEOTHERMIQUE = 5;
    case AUTRE_SYSTEME_THERMODYNAMIQUE_ELECTRIQUE = 6;
    case AUTRE_SYSTEME_THERMODYNAMIQUE_GAZ = 7;
    case AUTRE = 8;
    case RESEAU_FROID_URBAIN = 9;

    public static function from_enum_type_generateur_fr_id(int $id): self
    {
        return match ($id) {
            1, 2, 3 => self::PAC_AIR_AIR,
            4, 5, 6, 7 => self::PAC_AIR_EAU,
            8, 9, 10, 11 => self::PAC_EAU_EAU,
            12, 13, 14, 15 => self::PAC_EAU_GLYCOLEE_EAU,
            16, 17, 18, 19 => self::PAC_GEOTHERMIQUE,
            20 => self::AUTRE_SYSTEME_THERMODYNAMIQUE_ELECTRIQUE,
            21 => self::AUTRE_SYSTEME_THERMODYNAMIQUE_GAZ,
            22 => self::AUTRE,
            23 => self::RESEAU_FROID_URBAIN,
        };
    }

    public function id(): int
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
            self::AUTRE_SYSTEME_THERMODYNAMIQUE_ELECTRIQUE => 'Autre système thermodynamique électrique',
            self::AUTRE_SYSTEME_THERMODYNAMIQUE_GAZ => 'Autre système thermodynamique gaz',
            self::AUTRE => 'Autre système de refroidissement',
            self::RESEAU_FROID_URBAIN => 'Réseau de froid urbain'
        };
    }
}
