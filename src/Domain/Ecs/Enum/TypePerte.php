<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum TypePerte: string implements Enum
{
    case DISTRIBUTION = 'DISTRIBUTION';
    case GENERATION = 'GENERATION';
    case STOCKAGE = 'STOCKAGE';

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::DISTRIBUTION => 'Pertes de distribution',
            self::GENERATION => 'Pertes de génération',
            self::STOCKAGE => 'Pertes de stockage',
        };
    }
}
