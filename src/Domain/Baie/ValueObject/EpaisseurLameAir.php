<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Baie\Enum\TypeVitrage;
use App\Domain\Common\ValueObject\Nombre;

/**
 * Epaisseur de la lame d'air en mm
 */
final class EpaisseurLameAir extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }

    public static function is_applicable_by_type_vitrage(TypeVitrage $type_vitrage): bool
    {
        return $type_vitrage !== TypeVitrage::SIMPLE_VITRAGE;
    }

    public static function is_requis_by_type_vitrage(TypeVitrage $type_vitrage): bool
    {
        return \in_array($type_vitrage, [
            TypeVitrage::DOUBLE_VITRAGE,
            TypeVitrage::DOUBLE_VITRAGE_FE,
            TypeVitrage::TRIPLE_VITRAGE,
            TypeVitrage::TRIPLE_VITRAGE_FE,
        ]);
    }
}
