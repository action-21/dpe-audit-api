<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeInstallation: int implements Enum
{
    case INSTALLATION_INDIVIDUELLE = 1;
    case INSTALLATION_COLLECTIVE = 2;

    public static function from_enum_type_installation_id(int $id): self
    {
        return match ($id) {
            1 => self::INSTALLATION_INDIVIDUELLE,
            2, 3, 4 => self::INSTALLATION_COLLECTIVE,
        };
    }

    /** @return self[] */
    public static function cases_by_type_generateur(TypeGenerateur $type_generateur): array
    {
        if (\in_array($type_generateur, [
            TypeGenerateur::CET_SUR_AIR_AMBIANT,
            TypeGenerateur::CET_SUR_AIR_EXTERIEUR,
            TypeGenerateur::CET_SUR_AIR_EXTRAIT,
            TypeGenerateur::ACCUMULATEUR_GAZ_CLASSIQUE,
            TypeGenerateur::ACCUMULATEUR_GAZ_CONDENSATION,
            TypeGenerateur::CHAUFFE_EAU_GAZ_PRODUCTION_INSTANTANEE,
            TypeGenerateur::BALLON_ELECTRIQUE_ACCUMULATION_HORIZONTAL,
            TypeGenerateur::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_AUTRES_OU_INCONNUE,
            TypeGenerateur::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_CATEGORIE_B_OU_2_ETOILES,
            TypeGenerateur::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_CATEGORIE_C_OU_3_ETOILES,
            TypeGenerateur::ACCUMULATEUR_GPL_PROPANE_BUTANE_CLASSIQUE,
            TypeGenerateur::ACCUMULATEUR_GPL_PROPANE_BUTANE_CONDENSATION,
            TypeGenerateur::CHAUFFE_EAU_GPL_PROPANE_BUTANE_PRODUCTION_INSTANTANEE,
            TypeGenerateur::CHAUFFE_EAU_ELECTRIQUE_INSTANTANE,
        ])) {
            return [self::INSTALLATION_INDIVIDUELLE];
        }
        return self::cases();
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::INSTALLATION_INDIVIDUELLE => 'Installation individuelle',
            self::INSTALLATION_COLLECTIVE => 'Installation collective',
        };
    }

    /**
     * Récupération des pertes de stockage
     */
    public function recuperation_pertes_stockage(): bool
    {
        return $this === self::INSTALLATION_INDIVIDUELLE;
    }
}
