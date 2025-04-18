<?php

namespace App\Domain\Enveloppe\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeDeperdition: string implements Enum
{
    case BAIE = 'baie';
    case MUR = 'mur';
    case PLANCHER_BAS = 'plancher_bas';
    case PLANCHER_HAUT = 'plancher_hauts';
    case PORTE = 'porte';
    case PONT_THERMIQUE = 'pont_thermique';
    case RENOUVELEMENT_AIR = 'renouvellement_air';

    public function id(): int|string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::BAIE => 'Déperdition par les baies',
            self::MUR => 'Déperdition par les murs',
            self::PLANCHER_BAS => 'Déperdition par les planchers bas',
            self::PLANCHER_HAUT => 'Déperdition par les planchers hauts',
            self::PORTE => 'Déperdition par les portes',
            self::PONT_THERMIQUE => 'Déperdition par les ponts thermiques',
            self::RENOUVELEMENT_AIR => 'Déperdition par renouvellement d\'air',
        };
    }
}
