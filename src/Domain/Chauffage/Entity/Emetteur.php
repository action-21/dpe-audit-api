<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Enum\{TemperatureDistribution, TypeEmetteur, TypeEmission};
use App\Domain\Common\Type\Id;
use Webmozart\Assert\Assert;

final class Emetteur
{
    public function __construct(
        private readonly Id $id,
        private readonly Chauffage $chauffage,
        private string $description,
        private TypeEmetteur $type,
        private TemperatureDistribution $temperature_distribution,
        private bool $presence_robinet_thermostatique,
        private ?int $annee_installation,
    ) {}

    public function reinitialise(): void {}

    public function controle(): void
    {
        Assert::nullOrLessThanEq($this->annee_installation, (int) date('Y'));
        Assert::nullOrGreaterThanEq($this->annee_installation, $this->chauffage->annee_construction_batiment());
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function chauffage(): Chauffage
    {
        return $this->chauffage;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type(): TypeEmetteur
    {
        return $this->type;
    }

    public function temperature_distribution(): TemperatureDistribution
    {
        return $this->temperature_distribution;
    }

    public function presence_robinet_thermostatique(): bool
    {
        return $this->presence_robinet_thermostatique;
    }

    public function annee_installation(): ?int
    {
        return $this->annee_installation;
    }

    public function type_emission(): TypeEmission
    {
        return TypeEmission::from_type_emetteur($this->type);
    }
}
