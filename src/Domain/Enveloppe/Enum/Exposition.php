<?php

namespace App\Domain\Enveloppe\Enum;

use App\Domain\Common\Enum\Enum;

enum Exposition: string implements Enum
{
    case EXPOSITION_SIMPLE = 'SIMPLE';
    case EXPOSITION_MULTIPLE = 'MULTIPLE';

    public static function from_plusieurs_facades_exposées(bool $plusieurs_facades_exposées): self
    {
        return match ($plusieurs_facades_exposées) {
            true => self::EXPOSITION_MULTIPLE,
            false => self::EXPOSITION_SIMPLE,
        };
    }

    public function id(): string
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

    public function e(): float
    {
        return match ($this) {
            self::EXPOSITION_SIMPLE => 0.02,
            self::EXPOSITION_MULTIPLE => 0.07,
        };
    }

    public function f(): float
    {
        return match ($this) {
            self::EXPOSITION_SIMPLE => 20,
            self::EXPOSITION_MULTIPLE => 15,
        };
    }
}
