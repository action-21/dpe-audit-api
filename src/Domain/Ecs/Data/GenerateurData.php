<?php

namespace App\Domain\Ecs\Data;

use App\Domain\Common\ValueObject\{Consommations, Emissions, Pertes, Pourcentage};
use Webmozart\Assert\Assert;

final class GenerateurData
{
    public function __construct(
        public readonly ?float $pecs,
        public readonly ?float $paux,
        public readonly ?float $pn,
        public readonly ?float $cop,
        public readonly ?Pourcentage $rpn,
        public readonly ?float $qp0,
        public readonly ?float $pveilleuse,
        public readonly ?Pertes $pertes,
        public readonly ?Pertes $pertes_recuperables,
        public readonly ?Consommations $consommations,
        public readonly ?Emissions $emissions,
    ) {}

    public static function create(
        ?float $pecs = null,
        ?float $paux = null,
        ?float $pn = null,
        ?float $cop = null,
        ?Pourcentage $rpn = null,
        ?float $qp0 = null,
        ?float $pveilleuse = null,
        ?Pertes $pertes = null,
        ?Pertes $pertes_recuperables = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        Assert::nullOrGreaterThanEq($pecs, 0);
        Assert::nullOrGreaterThanEq($paux, 0);
        Assert::nullOrGreaterThan($pn, 0);
        Assert::nullOrGreaterThan($cop, 0);
        Assert::nullOrGreaterThan($qp0, 0);
        Assert::nullOrGreaterThanEq($pveilleuse, 0);

        return new self(
            pecs: $pecs,
            paux: $paux,
            pn: $pn,
            cop: $cop,
            rpn: $rpn,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
            pertes: $pertes,
            pertes_recuperables: $pertes_recuperables,
            consommations: $consommations,
            emissions: $emissions,
        );
    }

    public function with(
        ?float $pecs = null,
        ?float $paux = null,
        ?float $pn = null,
        ?float $cop = null,
        ?Pourcentage $rpn = null,
        ?float $qp0 = null,
        ?float $pveilleuse = null,
        ?Pertes $pertes = null,
        ?Pertes $pertes_recuperables = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        return self::create(
            pecs: $pecs ?? $this->pecs,
            paux: $paux ?? $this->paux,
            pn: $pn ?? $this->pn,
            cop: $cop ?? $this->cop,
            rpn: $rpn ?? $this->rpn,
            qp0: $qp0 ?? $this->qp0,
            pveilleuse: $pveilleuse ?? $this->pveilleuse,
            pertes: $pertes ? ($this->pertes?->merge($pertes) ?? $pertes) : $this->pertes,
            pertes_recuperables: $pertes_recuperables ? ($this->pertes_recuperables?->merge($pertes_recuperables) ?? $pertes_recuperables) : $this->pertes_recuperables,
            consommations: $consommations ? ($this->consommations?->merge($consommations) ?? $consommations) : $this->consommations,
            emissions: $emissions ? ($this->emissions?->merge($emissions) ?? $emissions) : $this->emissions,
        );
    }
}
