<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Common\ValueObject\{Id, Pourcentage};

final class ReseauChaleur
{
    public function __construct(
        public readonly Id $id,
        public readonly Pourcentage $contenu_co2,
        public readonly Pourcentage $contenu_co2_acv,
        public readonly Pourcentage $taux_enr,
    ) {}

    public function id(): Id
    {
        return $this->id;
    }

    public function contenu_co2(): Pourcentage
    {
        return $this->contenu_co2;
    }

    public function contenu_co2_acv(): Pourcentage
    {
        return $this->contenu_co2_acv;
    }

    public function taux_enr(): Pourcentage
    {
        return $this->taux_enr;
    }
}
