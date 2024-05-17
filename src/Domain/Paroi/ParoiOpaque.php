<?php

namespace App\Domain\Paroi;

use App\Domain\Common\ValueObject\{Entier, Nombre};

interface ParoiOpaque extends Paroi
{
    /**
     * Surface totale de la paroi en m²
     */
    public function surface(): float;

    /**
     * Paroi verticale en contact avec l'extérieur
     */
    public function facade(): bool;

    /**
     * Etat d'inertie de la proi
     */
    public function paroi_lourde(): bool;

    /**
     * Orientation de la paroi
     */
    public function orientation(): null|Entier|Nombre;
}
