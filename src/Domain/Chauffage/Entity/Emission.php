<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Enum\{EquipementIntermittence, TemperatureDistribution, TypeDistribution, TypeEmission, TypeRegulation};
use App\Domain\Chauffage\InstallationChauffage;
use App\Domain\Chauffage\ValueObject\{AnneeInstallation, Surface};
use App\Domain\Common\ValueObject\Id;

final class Emission
{
    public function __construct(
        private readonly Id $id,
        private readonly Generateur $generateur,
        private string $description,
        private Surface $surface,
        private TypeDistribution $type_distribution,
        private TypeEmission $type_emission,
        private TypeRegulation $type_regulation,
        private EquipementIntermittence $equipement_intermittence,
        private ?bool $reseau_distribution_isole = null,
        private ?TemperatureDistribution $temperature_distribution = null,
        private ?AnneeInstallation $annee_installation = null,
    ) {
    }

    public static function create_emission_directe(
        Generateur $generateur,
        TypeEmission $type_emission,
        TypeRegulation $type_regulation,
        EquipementIntermittence $equipement_intermittence,
    ): self {
        return (new self(
            id: Id::create(),
            generateur: $generateur,
            description: $generateur->description(),
            surface: $generateur->installation()->surface(),
            type_distribution: TypeDistribution::SANS,
            type_emission: $type_emission,
            type_regulation: $type_regulation,
            equipement_intermittence: $equipement_intermittence,
        ))->set_emission_directe(
            type_emission: $type_emission,
            type_regulation: $type_regulation,
            equipement_intermittence: $equipement_intermittence,
        );
    }

    public static function create_reseau_hydraulique(
        Generateur $generateur,
        string $description,
        Surface $surface,
        TypeEmission $type_emission,
        TypeRegulation $type_regulation,
        TemperatureDistribution $temperature_distribution,
        EquipementIntermittence $equipement_intermittence,
        bool $reseau_distribution_isole,
        ?AnneeInstallation $annee_installation = null,
    ): self {
        return (new self(
            id: Id::create(),
            generateur: $generateur,
            description: $description,
            surface: $surface,
            type_distribution: TypeDistribution::RESEAU_HYDRAULIQUE,
            type_emission: $type_emission,
            type_regulation: $type_regulation,
            equipement_intermittence: $equipement_intermittence,
            reseau_distribution_isole: $reseau_distribution_isole,
            temperature_distribution: $temperature_distribution,
            annee_installation: $annee_installation,
        ))->set_reseau_hydraulique(
            surface: $surface,
            type_emission: $type_emission,
            type_regulation: $type_regulation,
            temperature_distribution: $temperature_distribution,
            equipement_intermittence: $equipement_intermittence,
            reseau_distribution_isole: $reseau_distribution_isole,
            annee_installation: $annee_installation,
        );
    }

    public static function create_reseau_aeraulique(
        Generateur $generateur,
        string $description,
        Surface $surface,
        TypeEmission $type_emission,
        TypeRegulation $type_regulation,
        EquipementIntermittence $equipement_intermittence,
        bool $reseau_distribution_isole,
        ?AnneeInstallation $annee_installation = null,
    ): self {
        return (new self(
            id: Id::create(),
            generateur: $generateur,
            description: $description,
            surface: $surface,
            type_distribution: TypeDistribution::RESEAU_AERAULIQUE,
            type_emission: $type_emission,
            type_regulation: $type_regulation,
            equipement_intermittence: $equipement_intermittence,
            reseau_distribution_isole: $reseau_distribution_isole,
            annee_installation: $annee_installation,
        ))->set_reseau_aeraulique(
            surface: $surface,
            type_emission: $type_emission,
            type_regulation: $type_regulation,
            equipement_intermittence: $equipement_intermittence,
            reseau_distribution_isole: $reseau_distribution_isole,
            annee_installation: $annee_installation,
        );
    }

    public static function create_reseau_fluide_frigorigene(
        Generateur $generateur,
        string $description,
        Surface $surface,
        TypeEmission $type_emission,
        TypeRegulation $type_regulation,
        EquipementIntermittence $equipement_intermittence,
        bool $reseau_distribution_isole,
        ?AnneeInstallation $annee_installation = null,
    ): self {
        return (new self(
            id: Id::create(),
            generateur: $generateur,
            description: $description,
            surface: $surface,
            type_distribution: TypeDistribution::RESEAU_FLUIDE_FRIGORIGENE,
            type_emission: $type_emission,
            type_regulation: $type_regulation,
            equipement_intermittence: $equipement_intermittence,
            reseau_distribution_isole: $reseau_distribution_isole,
            annee_installation: $annee_installation,
        ))->set_reseau_fluide_frigorigene(
            surface: $surface,
            type_emission: $type_emission,
            type_regulation: $type_regulation,
            equipement_intermittence: $equipement_intermittence,
            reseau_distribution_isole: $reseau_distribution_isole,
            annee_installation: $annee_installation,
        );
    }

    public function update(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function set_emission_directe(
        TypeEmission $type_emission,
        TypeRegulation $type_regulation,
        EquipementIntermittence $equipement_intermittence,
    ): self {
        if (!\in_array(TypeDistribution::SANS, TypeDistribution::cases_by_type_generateur($this->generateur()->type_generateur()))) {
            throw new \DomainException('Le type de distribution n\'est pas compatible avec le générateur associé');
        }
        $this->type_distribution = TypeDistribution::SANS;
        $this->surface = $this->installation()->surface();
        $this->type_emission = $type_emission;
        $this->type_regulation = $type_regulation;
        $this->equipement_intermittence = $equipement_intermittence;
        $this->temperature_distribution = null;
        $this->reseau_distribution_isole = null;
        $this->annee_installation = null;
        $this->controle_coherence();
        return $this;
    }

    public function set_reseau_hydraulique(
        Surface $surface,
        TypeEmission $type_emission,
        TypeRegulation $type_regulation,
        TemperatureDistribution $temperature_distribution,
        EquipementIntermittence $equipement_intermittence,
        bool $reseau_distribution_isole,
        ?AnneeInstallation $annee_installation = null,
    ): self {
        if (!\in_array(TypeDistribution::RESEAU_HYDRAULIQUE, TypeDistribution::cases_by_type_generateur($this->generateur()->type_generateur()))) {
            throw new \DomainException('Le type de distribution n\'est pas compatible avec le générateur associé');
        }
        $this->type_distribution = TypeDistribution::RESEAU_HYDRAULIQUE;
        $this->surface = $surface;
        $this->type_emission = $type_emission;
        $this->type_regulation = $type_regulation;
        $this->temperature_distribution = $temperature_distribution;
        $this->equipement_intermittence = $equipement_intermittence;
        $this->reseau_distribution_isole = $reseau_distribution_isole;
        $this->annee_installation = $annee_installation;
        $this->controle_coherence();
        return $this;
    }

    public function set_reseau_aeraulique(
        Surface $surface,
        TypeEmission $type_emission,
        TypeRegulation $type_regulation,
        EquipementIntermittence $equipement_intermittence,
        bool $reseau_distribution_isole,
        ?AnneeInstallation $annee_installation = null,
    ): self {
        if (!\in_array(TypeDistribution::RESEAU_AERAULIQUE, TypeDistribution::cases_by_type_generateur($this->generateur()->type_generateur()))) {
            throw new \DomainException('Le type de distribution n\'est pas compatible avec le générateur associé');
        }
        $this->type_distribution = TypeDistribution::RESEAU_AERAULIQUE;
        $this->surface = $surface;
        $this->type_emission = $type_emission;
        $this->type_regulation = $type_regulation;
        $this->equipement_intermittence = $equipement_intermittence;
        $this->reseau_distribution_isole = $reseau_distribution_isole;
        $this->annee_installation = $annee_installation;
        $this->temperature_distribution = null;
        $this->controle_coherence();
        return $this;
    }

    public function set_reseau_fluide_frigorigene(
        Surface $surface,
        TypeEmission $type_emission,
        TypeRegulation $type_regulation,
        EquipementIntermittence $equipement_intermittence,
        bool $reseau_distribution_isole,
        ?AnneeInstallation $annee_installation = null,
    ): self {
        if (!\in_array(TypeDistribution::RESEAU_FLUIDE_FRIGORIGENE, TypeDistribution::cases_by_type_generateur($this->generateur()->type_generateur()))) {
            throw new \DomainException('Le type de distribution n\'est pas compatible avec le générateur associé');
        }
        $this->type_distribution = TypeDistribution::RESEAU_FLUIDE_FRIGORIGENE;
        $this->surface = $surface;
        $this->type_emission = $type_emission;
        $this->type_regulation = $type_regulation;
        $this->equipement_intermittence = $equipement_intermittence;
        $this->reseau_distribution_isole = $reseau_distribution_isole;
        $this->annee_installation = $annee_installation;
        $this->temperature_distribution = null;
        $this->controle_coherence();
        return $this;
    }

    public function controle_coherence(): void
    {
        $type_emission_cases = \array_intersect(
            TypeEmission::cases_by_type_distribution($this->type_distribution),
            TypeEmission::cases_by_type_generateur($this->generateur()->type_generateur())
        );
        if (\count($type_emission_cases) === 1) {
            $this->type_emission = \reset($type_emission_cases);
        }
        if (!\in_array($this->type_emission, $type_emission_cases)) {
            throw new \DomainException('Le type d\'émission n\'est pas compatible avec le type de distribution');
        }
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function installation(): InstallationChauffage
    {
        return $this->generateur->installation();
    }

    public function generateur(): Generateur
    {
        return $this->generateur;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function surface(): Surface
    {
        return $this->surface;
    }

    public function type_distribution(): TypeDistribution
    {
        return $this->type_distribution;
    }

    public function reseau_distribution_isole(): ?bool
    {
        return $this->reseau_distribution_isole;
    }

    public function type_emission(): TypeEmission
    {
        return $this->type_emission;
    }

    public function type_regulation(): TypeRegulation
    {
        return $this->type_regulation;
    }

    public function equipement_intermittence(): EquipementIntermittence
    {
        return $this->equipement_intermittence;
    }

    public function temperature_distribution(): ?TemperatureDistribution
    {
        return $this->temperature_distribution;
    }

    public function annee_installation(): ?AnneeInstallation
    {
        return $this->annee_installation;
    }
}
