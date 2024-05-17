<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeInstallation: int implements Enum
{
    case INSTALLATION_INDIVIDUELLE = 1;
    case INSTALLATION_COLLECTIVE = 2;
    case INSTALLATION_COLLECTIVE_MULTI_BATIMENT = 3;
    case INSTALLATION_MIXTE = 4;

    public static function from_enum_type_installation_id(int $id): self
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
            self::INSTALLATION_INDIVIDUELLE => 'Installation individuelle',
            self::INSTALLATION_COLLECTIVE => 'Installation collective',
            self::INSTALLATION_COLLECTIVE_MULTI_BATIMENT => 'Installation collective multi-bâtiment : modélisée comme un réseau de chaleur',
            self::INSTALLATION_MIXTE => 'Installation hybride collective-individuelle (chauffage base + appoint individuel ou convecteur bi-jonction)'
        };
    }

    /**
     * Récupération des pertes de stockage
     */
    public function recuperation_pertes_stockage(): bool
    {
        return $this === self::INSTALLATION_INDIVIDUELLE;
    }

    /**
     * Installation individuelle pour le calcul des pertes de distribution
     */
    public function installation_individuelle(): bool
    {
        return $this === self::INSTALLATION_INDIVIDUELLE;
    }

    /**
     * Installation collective pour le calcul des pertes de distribution
     */
    public function installation_collective(): bool
    {
        return $this !== self::INSTALLATION_INDIVIDUELLE;
    }

    public function isolation_reseau_distribution_requis(): bool
    {
        return $this === self::INSTALLATION_COLLECTIVE;
    }
}
