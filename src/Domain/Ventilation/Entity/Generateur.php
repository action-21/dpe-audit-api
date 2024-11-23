<?php

namespace App\Domain\Ventilation\Entity;

use App\Domain\Common\Service\Assert;
use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVentilation};
use App\Domain\Ventilation\Ventilation;

final class Generateur
{
    public function __construct(
        private readonly Id $id,
        private readonly Ventilation $ventilation,
        private string $description,
        private TypeVentilation $type_ventilation,
        private TypeGenerateur $type,
        private bool $presence_echangeur_thermique,
        private bool $generateur_collectif,
        private ?int $annee_installation,
    ) {}

    public function set_generateur_centralise(
        TypeGenerateur\TypeGenerateurCentralise $type,
        bool $presence_echangeur_thermique,
        bool $generateur_collectif,
    ): self {
        $this->type = $type->to();
        $this->presence_echangeur_thermique = $presence_echangeur_thermique;
        $this->generateur_collectif = $generateur_collectif;
        $this->type_ventilation = TypeVentilation::VENTILATION_MECANIQUE_CENTRALISEE;
        return $this;
    }

    public function set_generateur_divise(TypeGenerateur\TypeGenerateurDivise $type,): self
    {
        $this->type = $type->to();
        $this->type_ventilation = TypeVentilation::VENTILATION_MECANIQUE_DIVISEE;
        $this->presence_echangeur_thermique = false;
        $this->generateur_collectif = false;
        return $this;
    }

    public function update(string $description, ?int $annee_installation): self
    {
        $this->description = $description;
        $this->annee_installation = $annee_installation;

        $this->controle();
        $this->reinitialise();
        return $this;
    }

    public function controle(): void
    {
        Assert::annee($this->annee_installation);
        Assert::superieur_ou_egal_a($this->annee_installation, $this->ventilation->annee_construction_batiment());
    }

    public function reinitialise(): void {}

    public function id(): Id
    {
        return $this->id;
    }

    public function ventilation(): Ventilation
    {
        return $this->ventilation;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type_ventilation(): TypeVentilation
    {
        return $this->type_ventilation;
    }

    public function type(): TypeGenerateur
    {
        return $this->type;
    }

    public function presence_echangeur_thermique(): bool
    {
        return $this->presence_echangeur_thermique;
    }

    public function generateur_collectif(): bool
    {
        return $this->generateur_collectif;
    }

    public function annee_installation(): ?int
    {
        return $this->annee_installation;
    }
}
