<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum LabelGenerateur: string implements Enum
{
    case FLAMME_VERTE = 'FLAMME_VERTE';
    case NF_PERFORMANCE = 'NF_PERFORMANCE';
    case SANS = 'SANS';
    case INCONNU = 'INCONNU';

    public static function from_enum_type_generateur_ch_id(int $id): ?self
    {
        return match ($id) {
            98, 99, 100 => self::NF_PERFORMANCE,
            28, 29, 30, 31, 44, 101, 102, 103, 104, 105 => self::SANS,
            32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 45, 46 => self::FLAMME_VERTE,
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
            self::FLAMME_VERTE => 'Label Flamme verte',
            self::NF_PERFORMANCE => 'Label NF Performance',
            self::SANS => 'Sans label',
            self::INCONNU => 'Inconnu'
        };
    }
}
