<?php

namespace App\Domain\Common\Enum;

enum TypePerte: string implements Enum
{
    case GENERATION = 'generation';
    case STOCKAGE = 'stockage';
    case DISTRIBUTION = 'distribution';

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::GENERATION => 'Génération',
            self::STOCKAGE => 'Stockage',
            self::DISTRIBUTION => 'Distribution',
        };
    }
}
