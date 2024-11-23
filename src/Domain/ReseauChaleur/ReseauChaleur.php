<?php

namespace App\Domain\ReseauChaleur;

use App\Domain\Common\Type\Id;

final class ReseauChaleur
{
    public function __construct(
        private readonly Id $id,
        private string $localisation,
        private string $nom,
        private float $contenu_co2,
        private float $contenu_co2_acv,
        private ?float $taux_enr,
    ) {}

    public function update(
        string $localisation,
        string $nom,
        float $contenu_co2,
        float $contenu_co2_acv,
        ?float $taux_enr,
    ): self {
        $this->localisation = $localisation;
        $this->nom = $nom;
        $this->contenu_co2 = $contenu_co2;
        $this->contenu_co2_acv = $contenu_co2_acv;
        $this->taux_enr = $taux_enr;
        return $this;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function localisation(): string
    {
        return $this->localisation;
    }

    public function nom(): string
    {
        return $this->nom;
    }

    public function contenu_co2(): float
    {
        return $this->contenu_co2;
    }

    public function contenu_co2_acv(): ?float
    {
        return $this->contenu_co2_acv;
    }

    public function taux_enr(): ?float
    {
        return $this->taux_enr;
    }
}
