<?php

namespace App\Domain\Baie\Entity;

use App\Domain\Baie\Baie;
use App\Domain\Baie\Enum\TypeMasqueProche;
use App\Domain\Common\Service\Assert;
use App\Domain\Common\Type\Id;

final class MasqueProche
{
    public function __construct(
        private readonly Id $id,
        private readonly Baie $baie,
        private string $description,
        private TypeMasqueProche $type_masque,
        private ?float $avancee = null,
    ) {}

    public function set_create_fond_balcon_ou_fond_flanc_loggias(float $avancee): self
    {
        $this->type_masque = TypeMasqueProche::FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS;
        $this->avancee = $avancee;
        $this->controle();
        return $this;
    }

    public function set_balcon_ou_auvent(float $avancee): self
    {
        $this->type_masque = TypeMasqueProche::BALCON_OU_AUVENT;
        $this->avancee = $avancee;
        $this->controle();
        return $this;
    }

    public function set_paroi_laterale(bool $retour_sud): self
    {
        $this->type_masque = $retour_sud
            ? TypeMasqueProche::PAROI_LATERALE_AVEC_OBSTACLE_AU_SUD
            : TypeMasqueProche::PAROI_LATERALE_SANS_OBSTACLE_AU_SUD;
        $this->avancee = null;

        $this->controle();
        return $this;
    }

    public function update(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function controle(): void
    {
        Assert::positif_ou_zero($this->avancee);

        if ($this->type_masque === TypeMasqueProche::FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS)
            Assert::non_null($this->baie->orientation());
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function baie(): Baie
    {
        return $this->baie;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type_masque(): TypeMasqueProche
    {
        return $this->type_masque;
    }

    public function avancee(): ?float
    {
        return $this->avancee;
    }
}
