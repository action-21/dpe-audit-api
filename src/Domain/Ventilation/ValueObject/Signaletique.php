<?php

namespace App\Domain\Ventilation\ValueObject;

use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVmc};

final class Signaletique
{
    public function __construct(
        public readonly TypeGenerateur $type,
        public readonly bool $presence_echangeur_thermique,
        public readonly ?TypeVmc $type_vmc = null,
    ) {}

    public static function create_vmc(
        TypeGenerateur\Vmc $type,
        TypeVmc $type_vmc,
        bool $presence_echangeur_thermique,
    ): self {
        return new self(
            type: $type->to(),
            type_vmc: $type_vmc,
            presence_echangeur_thermique: $presence_echangeur_thermique,
        );
    }

    public static function create_ventilation_mecanique(): self
    {
        return new self(
            type: TypeGenerateur::VENTILATION_MECANIQUE,
            presence_echangeur_thermique: false,
        );
    }

    public static function create_puit_climatique(bool $presence_echangeur_thermique,): self
    {
        return new self(
            type: TypeGenerateur::PUIT_CLIMATIQUE,
            presence_echangeur_thermique: $presence_echangeur_thermique,
        );
    }

    public static function create_vmi(): self
    {
        return new self(
            type: TypeGenerateur::VMI,
            presence_echangeur_thermique: false,
        );
    }

    public static function create_vmr(): self
    {
        return new self(
            type: TypeGenerateur::VMR,
            presence_echangeur_thermique: false,
        );
    }
}
