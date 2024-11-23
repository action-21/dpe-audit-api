<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum TemperatureDistribution: string implements Enum
{
    case BASSE_TEMPERATURE = 'BASSE_TEMPERATURE';
    case MOYENNE_TEMPERATURE = 'MOYENNE_TEMPERATURE';
    case HAUTE_TEMPERATURE = 'HAUTE_TEMPERATURE';

    public static function from_enum_temp_distribution_ch_id(int $id): self
    {
        return match ($id) {
            2 => self::BASSE_TEMPERATURE,
            3 => self::MOYENNE_TEMPERATURE,
            4 => self::HAUTE_TEMPERATURE,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::BASSE_TEMPERATURE => 'Basse',
            self::MOYENNE_TEMPERATURE => 'Moyenne',
            self::HAUTE_TEMPERATURE => 'Haute'
        };
    }

    /**
     * Chute nominale de température de dimensionnement en °C
     */
    public function chute_nominale_temperature(): float
    {
        return match ($this) {
            self::BASSE_TEMPERATURE => 7.5,
            self::MOYENNE_TEMPERATURE => 7.5,
            self::HAUTE_TEMPERATURE => 15,
        };
    }
}
