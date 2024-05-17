<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\ValueObject\Entier;

/**
 * Nombre de niveaux desservis par l'installation d'ECS
 */
final class NiveauxDesservis extends Entier
{
    public static function from(int $valeur): static
    {
        return static::_from(valeur: $valeur);
    }
}
