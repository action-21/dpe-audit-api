<?php

namespace App\Domain\Enveloppe;

use App\Domain\Enveloppe\Enum\{ConfortEte, Inertie, Performance};
use App\Domain\Enveloppe\ValueObject\{Apports, Deperdition, Deperditions, Permeabilite, SurfaceDeperditive, SurfacesDeperditives};

final class EnveloppeData
{
    public function __construct(
        public readonly ?SurfacesDeperditives $surfaces_deperditives,
        public readonly ?Permeabilite $permeabilite,
        public readonly ?Deperditions $deperditions,
        public readonly ?float $ubat,
        public readonly ?bool $inertie_lourde,
        public readonly ?bool $planchers_hauts_isoles,
        public readonly ?bool $presence_protections_solaires,
        public readonly ?bool $logement_traversant,
        public readonly ?bool $presence_brasseurs_air,
        public readonly ?ConfortEte $confort_ete,
        public readonly ?Performance $performance,
        public readonly ?Inertie $inertie,
        public readonly ?Apports $apports,
    ) {}

    public static function create(
        ?SurfacesDeperditives $surfaces_deperditives = null,
        ?Permeabilite $permeabilite = null,
        ?Deperditions $deperditions = null,
        ?float $ubat = null,
        ?bool $inertie_lourde = null,
        ?bool $planchers_hauts_isoles = null,
        ?bool $presence_protections_solaires = null,
        ?bool $logement_traversant = null,
        ?bool $presence_brasseurs_air = null,
        ?ConfortEte $confort_ete = null,
        ?Performance $performance = null,
        ?Inertie $inertie = null,
        ?Apports $apports = null,
    ): self {
        return new self(
            surfaces_deperditives: $surfaces_deperditives,
            permeabilite: $permeabilite,
            deperditions: $deperditions,
            ubat: $ubat,
            inertie_lourde: $inertie_lourde,
            planchers_hauts_isoles: $planchers_hauts_isoles,
            presence_protections_solaires: $presence_protections_solaires,
            logement_traversant: $logement_traversant,
            presence_brasseurs_air: $presence_brasseurs_air,
            confort_ete: $confort_ete,
            performance: $performance,
            inertie: $inertie,
            apports: $apports,
        );
    }

    public function with(
        ?SurfacesDeperditives $surfaces_deperditives = null,
        ?Permeabilite $permeabilite = null,
        ?Deperditions $deperditions = null,
        ?float $ubat = null,
        ?bool $inertie_lourde = null,
        ?bool $planchers_hauts_isoles = null,
        ?bool $presence_protections_solaires = null,
        ?bool $logement_traversant = null,
        ?bool $presence_brasseurs_air = null,
        ?ConfortEte $confort_ete = null,
        ?Performance $performance = null,
        ?Inertie $inertie = null,
        ?Apports $apports = null,
    ): self {
        return self::create(
            ubat: $ubat ?? $this->ubat,
            performance: $performance ?? $this->performance,
            inertie_lourde: $inertie_lourde ?? $this->inertie_lourde,
            planchers_hauts_isoles: $planchers_hauts_isoles ?? $this->planchers_hauts_isoles,
            presence_protections_solaires: $presence_protections_solaires ?? $this->presence_protections_solaires,
            logement_traversant: $logement_traversant ?? $this->logement_traversant,
            presence_brasseurs_air: $presence_brasseurs_air ?? $this->presence_brasseurs_air,
            confort_ete: $confort_ete ?? $this->confort_ete,
            inertie: $inertie ?? $this->inertie,
            permeabilite: $permeabilite ?? $this->permeabilite,
            apports: $apports ?? $this->apports,
            surfaces_deperditives: $surfaces_deperditives ?? $this->surfaces_deperditives,
            deperditions: $deperditions ?? $this->deperditions,
        );
    }

    public function add_surface_deperditive(SurfaceDeperditive $surface_deperditive): self
    {
        return $this->with(
            surfaces_deperditives: $this->surfaces_deperditives?->add($surface_deperditive)
                ?? SurfacesDeperditives::create($surface_deperditive)
        );
    }

    public function add_deperdition(Deperdition $deperdition): self
    {
        return $this->with(
            deperditions: $this->deperditions?->add($deperdition) ?? Deperditions::create($deperdition)
        );
    }
}
