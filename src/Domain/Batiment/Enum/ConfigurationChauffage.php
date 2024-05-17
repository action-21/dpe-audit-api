<?php

namespace App\Domain\Batiment\Enum;

use App\Domain\Common\Enum\Enum;

/**
 * Configuration de l'installation de chauffage d'un bâtiment collectif
 */
enum ConfigurationChauffage: int implements Enum
{
    case INDIVIDUEL = 1;
    case COLLECTIF = 2;
    case MIXTE = 3;

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::INDIVIDUEL => 'Individuel',
            self::COLLECTIF => 'Collectif',
            self::MIXTE => 'Mixte',
        };
    }

    public function individuel(): bool
    {
        return $this->value === self::INDIVIDUEL;
    }

    public function collectif(): bool
    {
        return $this->value === self::COLLECTIF;
    }

    public function mixte(): bool
    {
        return $this->value === self::MIXTE;
    }
}
