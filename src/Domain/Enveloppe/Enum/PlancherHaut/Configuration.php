<?php

namespace App\Domain\Enveloppe\Enum\PlancherHaut;

use App\Domain\Common\Enum\Enum;

enum Configuration: string implements Enum
{
    case COMBLES_PERDUS = 'combles_perdus';
    case RAMPANTS = 'rampants';
    case TERRASSE = 'terrasse';

    public static function from_type_plancher_haut(TypePlancherHaut $type_plancher_haut): ?self
    {
        return match ($type_plancher_haut) {
            TypePlancherHaut::COMBLES_AMENAGES_SOUS_RAMPANT => self::RAMPANTS,
            TypePlancherHaut::TOITURE_CHAUME => self::RAMPANTS,
            TypePlancherHaut::BAC_ACIER => self::RAMPANTS,
            default => null,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::COMBLES_PERDUS => 'Plancher sous combles perdus',
            self::RAMPANTS => 'Rampants',
            self::TERRASSE => 'Terrasse',
        };
    }
}
