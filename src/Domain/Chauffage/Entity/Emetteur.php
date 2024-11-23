<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Enum\{TemperatureDistribution, TypeEmetteur, TypeEmission};
use App\Domain\Common\Service\Assert;
use App\Domain\Common\Type\Id;

/**
 * Émetteur hydraulique
 */
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

    public function update(
        string $description,
        TypeEmetteur $type,
        TemperatureDistribution $temperature_distribution,
        bool $presence_robinet_thermostatique,
        ?int $annee_installation,
    ): self {
        $this->description = $description;
        $this->type = $type;
        $this->temperature_distribution = $temperature_distribution;
        $this->presence_robinet_thermostatique = $presence_robinet_thermostatique;
        $this->annee_installation = $annee_installation;

        $this->controle();
        $this->reinitialise();
        return $this;
    }

    public function reinitialise(): void {}

    public function controle(): void
    {
        Assert::annee($this->annee_installation);
        Assert::superieur_ou_egal_a($this->annee_installation, $this->chauffage->annee_construction_batiment());
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
