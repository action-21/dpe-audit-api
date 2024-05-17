<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\ValueObject\Entier;

/**
 * Nombre de niveaux desservis par l'installation de chauffage
 */
final class NiveauxDesservis extends Entier
{
    public static function from(int $valeur): static
    {
        return static::_from(valeur: $valeur);
    }
}
