<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum UsageGenerateur: int implements Enum
{
    case ECS = 2;
    case MIXTE = 3;

    public static function from_enum_usage_generateur_id(int $id): self
    {
        return self::from($id);
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
            return [self::ECS];
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
            self::ECS => 'ECS',
            self::MIXTE => 'Chauffage + ECS'
        };
    }
}
