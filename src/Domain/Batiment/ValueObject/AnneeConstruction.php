<?php

namespace App\Domain\Batiment\ValueObject;

use App\Domain\Common\ValueObject\Entier;

final class AnneeConstruction extends Entier
{
    public static function from(int $valeur): static
    {
        return static::_from(valeur: $valeur, min: 1900, max: \date("Y"));
    }

    public function annee_isolation_defaut(): int
    {
        return $this->valeur <= 1988 ? 1975 : $this->valeur;
    }
}
