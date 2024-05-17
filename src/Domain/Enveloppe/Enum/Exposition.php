<?php

namespace App\Domain\Enveloppe\Enum;

use App\Domain\Common\Enum\Enum;

enum Exposition: int implements Enum
{
    case EXPOSITION_SIMPLE = 1;
    case EXPOSITION_MULTIPLE = 2;

    public static function from_boolean(bool $plusieurs_facades_exposées): self
    {
        return match ($plusieurs_facades_exposées) {
            true => self::EXPOSITION_MULTIPLE,
            false => self::EXPOSITION_SIMPLE,
        };
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::EXPOSITION_SIMPLE => 'Une seule façade exposée',
            self::EXPOSITION_MULTIPLE => 'Plusieurs façades exposées',
        };
    }

    /**
     * Coefficient de protection e
     */
    public function coefficient_e(): float
    {
        return match ($this) {
            self::EXPOSITION_SIMPLE => 0.02,
            self::EXPOSITION_MULTIPLE => 0.07,
        };
    }

    /**
     * Coefficient de protection f
     */
    public function coefficient_f(): float
    {
        return match ($this) {
            self::EXPOSITION_SIMPLE => 20,
            self::EXPOSITION_MULTIPLE => 15,
        };
    }
}
