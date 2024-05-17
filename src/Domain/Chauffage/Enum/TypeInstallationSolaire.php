<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeInstallationSolaire: int implements Enum
{
    case CHAUFFAGE = 1;
    case CHAUFFAGE_ECS = 4;

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
            self::CHAUFFAGE => 'Chauffage solaire',
            self::CHAUFFAGE_ECS => 'Chauffage + ECS solaire'
        };
    }
}
