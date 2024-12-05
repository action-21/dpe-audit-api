<?php

namespace App\Domain\Ventilation\Entity;

use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVentilation};
use App\Domain\Ventilation\Ventilation;
use Webmozart\Assert\Assert;

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

    public function controle(): void
    {
        Assert::lessThanEq($this->annee_installation, (int) date('Y'));
        Assert::greaterThanEq($this->annee_installation, $this->ventilation->annee_construction_batiment());
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
