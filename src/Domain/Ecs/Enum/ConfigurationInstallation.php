<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum ConfigurationInstallation: int implements Enum
{
    case INSTALLATION_SIMPLE_SANS_SOLAIRE = 1;
    case INSTALLATION_SIMPLE_AVEC_SOLAIRE = 2;
    case INSTALLATION_MULTIPLE = 3;

    public static function from_enum_cfg_installation_ecs_id(int $id): self
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
            self::INSTALLATION_SIMPLE_SANS_SOLAIRE => 'Un seul système d\'ECS sans solaire',
            self::INSTALLATION_SIMPLE_AVEC_SOLAIRE => 'Un seul système d\'ECS avec solaire',
            self::INSTALLATION_MULTIPLE => 'Deux systèmes d\'ECS dans une maison ou un appartement'
        };
    }

    /**
     * Ratio de l'installation en fonction de la configuration pris pour application de la section 11.4 de la méthode 3CL-DPE
     */
    public function ratio(): float
    {
        return match ($this) {
            self::INSTALLATION_SIMPLE_SANS_SOLAIRE => 1,
            self::INSTALLATION_SIMPLE_AVEC_SOLAIRE => 1,
            self::INSTALLATION_MULTIPLE => 0.5
        };
    }
}
