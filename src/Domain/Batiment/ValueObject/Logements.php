<?php

namespace App\Domain\Batiment\ValueObject;

use App\Domain\Common\ValueObject\Entier;

final class Logements extends Entier
{
    public static function from(int $valeur): static
    {
        return static::_from($valeur);
    }
}
