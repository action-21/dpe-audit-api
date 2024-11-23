<?php

namespace App\Database\Opendata\Baie;

use App\Database\Opendata\XMLReader;
use App\Domain\Baie\Enum\TypeMasqueProche;
use App\Domain\Common\Type\Id;

final class XMLMasqueProcheReader extends XMLReader
{
    public function apply(): bool
    {
        return $this->tv_coef_masque_proche_id() !== 19;
    }

    public function id(): Id
    {
        return Id::create();
    }

    public function description(): string
    {
        return 'Masque proche';
    }

    public function type_masque(): TypeMasqueProche
    {
        return match ($this->tv_coef_masque_proche_id()) {
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 => TypeMasqueProche::FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS,
            13, 14, 15, 16 => TypeMasqueProche::BALCON_OU_AUVENT,
            17 => TypeMasqueProche::PAROI_LATERALE_SANS_OBSTACLE_AU_SUD,
            18 => TypeMasqueProche::PAROI_LATERALE_AVEC_OBSTACLE_AU_SUD,
        };
    }

    public function avancee(): ?float
    {
        return match ($this->tv_coef_masque_proche_id()) {
            1, 5, 9, 13 => 0.5,
            2, 6, 10, 14 => 1.5,
            3, 7, 11, 15 => 2.5,
            4, 8, 12, 16 => 3.5,
            default => null,
        };
    }

    public function tv_coef_masque_proche_id(): int
    {
        return $this->xml()->findOneOrError('.//tv_coef_masque_proche_id')->intval();
    }
}
