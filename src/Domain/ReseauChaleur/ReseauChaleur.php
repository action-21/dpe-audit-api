<?php

namespace App\Domain\ReseauChaleur;

use App\Domain\Common\ValueObject\Id;

final class ReseauChaleur
{
    public function __construct(
        private readonly Id $id,
        private string $code_departement,
        private string $localisation,
        private string $nom,
        private float $contenu_co2,
        private ?float $contenu_co2_acv,
        private ?float $taux_enr,
    ) {
    }

    public static function create(
        string $code,
        string $code_departement,
        string $localisation,
        string $nom,
        float $contenu_co2,
        ?float $contenu_co2_acv,
        ?float $taux_enr,
    ): self {
        return new self(
            Id::from($code),
            $code_departement,
            $localisation,
            $nom,
            $contenu_co2,
            $contenu_co2_acv,
            $taux_enr,
        );
    }

    public function update(
        string $code_departement,
        string $localisation,
        string $nom,
        float $contenu_co2,
        ?float $contenu_co2_acv,
        ?float $taux_enr,
    ): self {
        $this->code_departement = $code_departement;
        $this->localisation = $localisation;
        $this->nom = $nom;
        $this->contenu_co2 = $contenu_co2;
        $this->contenu_co2_acv = $contenu_co2_acv;
        $this->taux_enr = $taux_enr;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function code_departement(): string
    {
        return $this->code_departement;
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
