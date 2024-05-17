<?php

namespace App\Domain\Logement\Enum;

use App\Domain\Common\Enum\Enum;

enum TypologieLogement: int implements Enum
{
    case T1 = 1;
    case T2 = 2;
    case T3 = 3;
    case T4 = 4;
    case T5 = 5;
    case T6 = 6;
    case T7 = 7;

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::T1 => 'T1',
            self::T2 => 'T2',
            self::T3 => 'T3',
            self::T4 => 'T4',
            self::T5 => 'T5',
            self::T6 => 'T6',
            self::T7 => 'T7 et plus',
        };
    }
}
