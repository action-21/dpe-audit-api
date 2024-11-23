<?php

namespace App\Domain\PlancherBas\Enum;

use App\Domain\Common\Enum\Enum;

enum Inertie: string implements Enum
{
    case INCONNU = 'INCONNU';
    case LOURDE = 'LOURDE';
    case LEGERE = 'LEGERE';

    public function id(): string
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

    public function est_lourde(): ?bool
    {
        return match ($this) {
            self::INCONNU => null,
            self::LEGERE => false,
            self::LOURDE => true,
        };
    }
}
