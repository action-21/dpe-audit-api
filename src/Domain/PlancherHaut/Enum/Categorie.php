<?php

namespace App\Domain\PlancherHaut\Enum;

use App\Domain\Common\Enum\Enum;

enum Categorie: string implements Enum
{
    case INCONNU = 'INCONNU';
    case COMBLES_PERDUS = 'COMBLES_PERDUS';
    case RAMPANTS = 'RAMPANTS';
    case TERRASSE = 'TERRASSE';

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::INCONNU => 'Inconnu',
            self::COMBLES_PERDUS => 'Plancher sous combles perdus',
            self::RAMPANTS => 'Rampants',
            self::TERRASSE => 'Terrasse',
        };
    }
}
