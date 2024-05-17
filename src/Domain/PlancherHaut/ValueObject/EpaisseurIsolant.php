<?php

namespace App\Domain\PlancherHaut\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Epaisseur de l'isolant en mm
 */
final class EpaisseurIsolant extends Nombre
{
    public static function from(float|int $valeur): static
    {
        return static::_from($valeur);
    }

    public function to_metre(): float
    {
        return $this->valeur() / 1000;
    }
}
