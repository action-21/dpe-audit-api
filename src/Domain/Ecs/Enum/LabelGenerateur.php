<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum LabelGenerateur: string implements Enum
{
    case NE_PERFORMANCE_A = 'ne_performance_a';
    case NE_PERFORMANCE_B = 'ne_performance_b';
    case NE_PERFORMANCE_C = 'ne_performance_c';

    public static function from_enum_type_generateur_ecs_id(int $id): ?self
    {
        return match ($id) {
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
            self::NE_PERFORMANCE_A => 'NF Performance - Catégorie A ou 1 étoile',
            self::NE_PERFORMANCE_B => 'NF Performance - Catégorie B ou 2 étoiles',
            self::NE_PERFORMANCE_C => 'NF Performance - Catégorie C ou 3 étoiles',
        };
    }
}
