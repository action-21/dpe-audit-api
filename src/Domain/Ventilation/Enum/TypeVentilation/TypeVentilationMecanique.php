<?php

namespace App\Domain\Ventilation\Enum\TypeVentilation;

use App\Domain\Ventilation\Enum\TypeVentilation;

enum TypeVentilationMecanique: string
{
    case VMC_SIMPLE_FLUX_AUTOREGLABLE = 'VMC_SIMPLE_FLUX_AUTOREGLABLE';
    case VMC_SIMPLE_FLUX_HYGROREGLABLE_TYPE_A = 'VMC_SIMPLE_FLUX_HYGROREGLABLE_TYPE_A';
    case VMC_SIMPLE_FLUX_HYGROREGLABLE_TYPE_B = 'VMC_SIMPLE_FLUX_HYGROREGLABLE_TYPE_B';
    case VMC_SIMPLE_FLUX_GAZ = 'VMC_SIMPLE_FLUX_GAZ';
    case VMC_BASSE_PRESSION_AUTOREGLABLE = 'VMC_BASSE_PRESSION_AUTOREGLABLE';
    case VMC_BASSE_PRESSION_HYGROREGLABLE_TYPE_A = 'VMC_BASSE_PRESSION_HYGROREGLABLE_TYPE_A';
    case VMC_BASSE_PRESSION_HYGROREGLABLE_TYPE_B = 'VMC_BASSE_PRESSION_HYGROREGLABLE_TYPE_B';
    case VMI = 'VMI';
    case VMR = 'VMR';

    public function to(): TypeVentilation
    {
        return TypeVentilation::from($this->value);
    }

    public function presence_entree_air_hygroreglable(): ?bool
    {
        return match ($this) {
            self::VMC_SIMPLE_FLUX_HYGROREGLABLE_TYPE_A, self::VMC_BASSE_PRESSION_HYGROREGLABLE_TYPE_A => false,
            self::VMC_SIMPLE_FLUX_HYGROREGLABLE_TYPE_B, self::VMC_BASSE_PRESSION_HYGROREGLABLE_TYPE_B => true,
            default => null,
        };
    }
}
