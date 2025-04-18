<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum UsageEcs: string implements Enum
{
    case ECS = 'ecs';
    case CHAUFFAGE_ECS = 'chauffage_ecs';

    public static function from_enum_usage_generateur_id(int $id): ?self
    {
        return match ($id) {
            2 => self::ECS,
            3 => self::CHAUFFAGE_ECS,
            default => null,
        };
    }

    public static function from_enum_type_installation_solaire_id(int $id): ?self
    {
        return match ($id) {
            2, 3 => self::ECS,
            4 => self::CHAUFFAGE_ECS,
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
            self::ECS => 'Eau chaude sanitaire',
            self::CHAUFFAGE_ECS => 'Chauffage et eau chaude sanitaire',
        };
    }
}
