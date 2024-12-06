<?php

namespace App\Domain\Ventilation\ValueObject;

use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVmc};

abstract class Signaletique
{
    public function __construct(
        public readonly TypeGenerateur $type,
        public readonly ?TypeVmc $type_vmc,
        public readonly bool $presence_echangeur_thermique,
        public readonly bool $generateur_collectif,
        public readonly ?int $annee_installation,
    ) {}
}
