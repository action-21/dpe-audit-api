<?php

namespace App\Domain\Mur\ValueObject;

use App\Domain\Mur\Enum\{Inertie, TypeDoublage, TypeMur};

final class Caracteristique
{
    public function __construct(
        public readonly TypeMur $type_mur,
        public readonly ?Epaisseur $epaisseur,
        public readonly TypeDoublage $type_doublage,
        public readonly Inertie $inertie,
        public readonly Surface $surface,
        public readonly bool $enduit_isolant,
        public readonly bool $paroi_ancienne,
        public readonly ?Umur0 $umur0,
        public readonly ?Umur $umur,
    ) {
    }
}
