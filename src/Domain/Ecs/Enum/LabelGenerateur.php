<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum LabelGenerateur: string implements Enum
{
    case INCONNU = 'INCONNU';
    case SANS = 'SANS';
    case NE_PERFORMANCE_A = 'NE_PERFORMANCE_A';
    case NE_PERFORMANCE_B = 'NE_PERFORMANCE_B';
    case NE_PERFORMANCE_C = 'NE_PERFORMANCE_C';

    public static function from_enum_type_generateur_ecs_id(int $id): ?self
    {
        return match ($id) {
            68, 69, 117 => self::INCONNU,
            70 => self::NE_PERFORMANCE_B,
            71 => self::NE_PERFORMANCE_C,
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
            self::INCONNU => 'Inconnu',
            self::SANS => 'Sans label',
            self::NE_PERFORMANCE_A => 'NF Performance - Catégorie A ou 1 étoile',
            self::NE_PERFORMANCE_B => 'NF Performance - Catégorie B ou 2 étoiles',
            self::NE_PERFORMANCE_C => 'NF Performance - Catégorie C ou 3 étoiles',
        };
    }
}
