<?php

namespace App\Domain\PlancherHaut\Enum;

use App\Domain\Common\Enum\Enum;

enum Inertie: int implements Enum
{
    case INCONNU = 1;
    case LOURDE = 2;
    case LEGERE = 3;

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::INCONNU => 'Inconnu',
            self::LOURDE => 'Lourde',
            self::LEGERE => 'Légère',
        };
    }

    public function lourde(): bool
    {
        return match ($this) {
            self::INCONNU, self::LEGERE => false,
            self::LOURDE => true,
        };
    }
}
