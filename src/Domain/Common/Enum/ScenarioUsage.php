<?php

namespace App\Domain\Common\Enum;

use App\Domain\Common\Enum\Enum;

enum ScenarioUsage: int implements Enum
{
    case CONVENTIONNEL = 1;
    case DEPENSIER = 2;

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::CONVENTIONNEL => 'Scénario conventionnel',
            self::DEPENSIER => 'Scénario dépensier',
        };
    }
}
