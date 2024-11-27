<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum TemperatureDistribution: string implements Enum
{
    case BASSE = 'BASSE';
    case MOYENNE = 'MOYENNE';
    case HAUTE = 'HAUTE';

    public static function from_enum_temp_distribution_ch_id(int $id): self
    {
        return match ($id) {
            2 => self::BASSE,
            3 => self::MOYENNE,
            4 => self::HAUTE,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::BASSE => 'Basse',
            self::MOYENNE => 'Moyenne',
            self::HAUTE => 'Haute'
        };
    }

    /**
     * Chute nominale de température de dimensionnement en °C
     */
    public function chute_nominale_temperature(): float
    {
        return match ($this) {
            self::BASSE => 7.5,
            self::MOYENNE => 7.5,
            self::HAUTE => 15,
        };
    }
}
