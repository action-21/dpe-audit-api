<?php

namespace App\Domain\PlancherBas\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Résistance thermique de l'isolant en m².K/W
 */
final class ResistanceIsolant extends Nombre
{
    public static function from(float|int $valeur): static
    {
        return static::_from($valeur);
    }
}
