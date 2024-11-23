<?php

namespace App\Domain\Enveloppe\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeParoi: string implements Enum
{
    case MUR = 'MUR';
    case PLANCHER_BAS = 'PLANCHER_BAS';
    case PLANCHER_HAUT = 'PLANCHER_HAUT';
    case BAIE = 'BAIE';
    case PORTE = 'PORTE';

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::MUR => 'Mur',
            self::PLANCHER_BAS => 'Plancher bas',
            self::PLANCHER_HAUT => 'Plancher haut',
            self::BAIE => 'Baie',
            self::PORTE => 'Porte'
        };
    }
}
