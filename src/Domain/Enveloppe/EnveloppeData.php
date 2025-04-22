<?php

namespace App\Domain\Enveloppe;

use App\Domain\Enveloppe\Enum\{Inertie, Performance};
use App\Domain\Enveloppe\ValueObject\{Apports, Deperdition, Deperditions, Permeabilite, SurfaceDeperditive, SurfacesDeperditives};

final class EnveloppeData
{
    public function __construct(
        public readonly ?SurfacesDeperditives $surfaces_deperditives,
        public readonly ?Permeabilite $permeabilite,
        public readonly ?Deperditions $deperditions,
        public readonly ?float $ubat,
        public readonly ?Performance $performance,
        public readonly ?Inertie $inertie,
        public readonly ?Apports $apports,
    ) {}

    public static function create(
        ?SurfacesDeperditives $surfaces_deperditives = null,
        ?Permeabilite $permeabilite = null,
        ?Deperditions $deperditions = null,
        ?float $ubat = null,
        ?Performance $performance = null,
        ?Inertie $inertie = null,
        ?Apports $apports = null,
    ): self {
        return new self(
            surfaces_deperditives: $surfaces_deperditives,
            permeabilite: $permeabilite,
            deperditions: $deperditions,
            ubat: $ubat,
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
        ?Performance $performance = null,
        ?Inertie $inertie = null,
        ?Apports $apports = null,
    ): self {
        return self::create(
            ubat: $ubat ?? $this->ubat,
            performance: $performance ?? $this->performance,
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
