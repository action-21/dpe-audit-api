<?php

namespace App\Domain\Ventilation\ValueObject;

use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVmc};

final class Signaletique
{
    public function __construct(
        public readonly TypeGenerateur $type,
        public readonly ?TypeVmc $type_vmc,
        public readonly bool $presence_echangeur_thermique,
        public readonly bool $generateur_collectif,
        public readonly ?int $annee_installation,
    ) {}

    public static function create(
        TypeGenerateur $type,
        bool $generateur_collectif,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur_thermique,
        ?int $annee_installation,
    ): self {
        if (\in_array($type, [
            TypeGenerateur::VMC_SIMPLE_FLUX,
            TypeGenerateur::VMC_BASSE_PRESSION,
            TypeGenerateur::VENTILATION_HYBRIDE,
        ])) {
            $type_vmc = $type_vmc ?? TypeVmc::AUTOREGLABLE;
        }
        if ($type === TypeGenerateur::VMR) {
            $generateur_collectif = false;
        }

        return new self(
            type: $type,
            type_vmc: $type_vmc,
            presence_echangeur_thermique: $presence_echangeur_thermique ?? false,
            generateur_collectif: $generateur_collectif,
            annee_installation: $annee_installation,
        );
    }
}
