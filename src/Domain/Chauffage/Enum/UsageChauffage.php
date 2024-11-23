<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum UsageChauffage: string implements Enum
{
    case CHAUFFAGE = 'CHAUFFAGE';
    case CHAUFFAGE_ECS = 'CHAUFFAGE_ECS';

    public static function from_enum_usage_generateur_id(int $id): self
    {
        return match ($id) {
            1 => self::CHAUFFAGE,
            3 => self::CHAUFFAGE_ECS,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::CHAUFFAGE => 'Chauffage',
            self::CHAUFFAGE_ECS => 'Chauffage + Eau chaude sanitaire'
        };
    }
}
