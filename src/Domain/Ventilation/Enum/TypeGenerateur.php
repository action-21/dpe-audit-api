<?php

namespace App\Domain\Ventilation\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeGenerateur: string implements Enum
{
    case VMC_SIMPLE_FLUX = 'vmc_simple_flux';
    case VMC_SIMPLE_FLUX_GAZ = 'vmc_simple_flux_gaz';
    case VMC_BASSE_PRESSION = 'vmc_basse_pression';
    case VMC_DOUBLE_FLUX = 'vmc_double_flux';
    case VMI = 'vmi';
    case VENTILATION_HYBRIDE = 'ventilation_hybride';
    case VENTILATION_MECANIQUE = 'ventilation_mecanique';
    case PUIT_CLIMATIQUE = 'puit_climatique';
    case VMR = 'vmr';

    public static function from_enum_type_ventilation_id(int $id): ?self
    {
        return match ($id) {
            3, 4, 5, 6, 7, 8, 9, 13, 14, 15 => self::VMC_SIMPLE_FLUX,
            10, 11, 12 => self::VMC_SIMPLE_FLUX_GAZ,
            16, 17, 18 => self::VMC_BASSE_PRESSION,
            19, 20, 21, 22, 23, 24 => self::VMC_DOUBLE_FLUX,
            26, 27, 28, 29, 30, 31 => self::VENTILATION_HYBRIDE,
            32, 33 => self::VENTILATION_MECANIQUE,
            35, 36, 37, 38 => self::PUIT_CLIMATIQUE,
            default => null,
        };
    }
    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::VMC_SIMPLE_FLUX => "VMC Simple flux",
            self::VMC_SIMPLE_FLUX_GAZ => "VMC Simple flux Gaz",
            self::VMC_BASSE_PRESSION => "VMC Basse pression",
            self::VMC_DOUBLE_FLUX => "VMC Double flux",
            self::VENTILATION_HYBRIDE => "Ventilation hybride",
            self::PUIT_CLIMATIQUE => "Puits climatique",
            self::VMI => "Ventilation mécanique par insufflation",
            self::VMR => "Ventilation répartie",
        };
    }

    public function is_generateur_collectif(): ?bool
    {
        return match ($this) {
            self::VMR => false,
            default => null,
        };
    }

    public function is_vmc(): bool
    {
        return match ($this) {
            self::VMC_SIMPLE_FLUX, self::VMC_SIMPLE_FLUX_GAZ, self::VMC_BASSE_PRESSION, self::VMC_DOUBLE_FLUX => true,
            default => false,
        };
    }
}
