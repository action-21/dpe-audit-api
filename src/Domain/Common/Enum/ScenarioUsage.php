<?php

namespace App\Domain\Common\Enum;

use App\Domain\Common\Enum\Enum;

enum ScenarioUsage: string implements Enum
{
    case CONVENTIONNEL = 'CONVENTIONNEL';
    case DEPENSIER = 'DEPENSIER';

    public function id(): string
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

    public static function each(\Closure $func): array
    {
        return \array_map($func, self::cases());
    }
}
