<?php

namespace App\Domain\Photovoltaique\Enum;

use App\Domain\Common\Enum\Enum;

enum InclinaisonCapteur: int implements Enum
{
    case LTE15 = 1;
    case GT15_LTE45 = 2;
    case GT45_LTE75 = 3;
    case GT75 = 4;

    public static function from_angle(int|float $angle): self
    {
        return match (true) {
            $angle <= 15 => self::LTE15,
            $angle <= 45 => self::GT15_LTE45,
            $angle <= 75 => self::GT45_LTE75,
            $angle > 75 => self::GT75,
        };
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::LTE15 => 'Inférieur ou égal à 15°',
            self::GT15_LTE45 => 'Entre 15° et 45°',
            self::GT45_LTE75 => 'Entre 45° et 75',
            self::GT75 => 'Supérieur à 75°'
        };
    }

    public function to_angle(): int
    {
        return match ($this) {
            self::LTE15 => 10,
            self::GT15_LTE45 => 30,
            self::GT45_LTE75 => 60,
            self::GT75 => 80,
        };
    }
}
