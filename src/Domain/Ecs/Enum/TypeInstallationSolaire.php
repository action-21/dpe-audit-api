<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeInstallationSolaire: int implements Enum
{
    case SOLAIRE_GT_5ANS = 2;
    case SOLAIRE_LTE_5ANS = 3;
    case INSTALLATION_MIXTE = 4;

    public static function from_enum_type_installation_solaire_id(int $id): self
    {
        return self::from($id);
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::SOLAIRE_GT_5ANS => 'ECS solaire seule supérieure à 5 ans',
            self::SOLAIRE_LTE_5ANS => 'ECS solaire seule inférieure ou égale à 5 ans',
            self::INSTALLATION_MIXTE => 'Chauffage + ECS solaire'
        };
    }
}
