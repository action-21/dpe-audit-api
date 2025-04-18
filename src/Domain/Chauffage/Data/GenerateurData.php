<?php

namespace App\Domain\Chauffage\Data;

use App\Domain\Common\ValueObject\{Consommations, Emissions, Pertes, Pourcentage};
use Webmozart\Assert\Assert;

final class GenerateurData
{
    public function __construct(
        public readonly ?float $pch,
        public readonly ?float $pn,
        public readonly ?float $paux,
        public readonly ?float $scop,
        public readonly ?Pourcentage $rpn,
        public readonly ?Pourcentage $rpint,
        public readonly ?float $qp0,
        public readonly ?float $pveilleuse,
        public readonly ?float $tfonc30,
        public readonly ?float $tfonc100,
        public readonly ?Pertes $pertes,
        public readonly ?Pertes $pertes_recuperables,
        public readonly ?Consommations $consommations,
        public readonly ?Emissions $emissions,
    ) {}

    public static function create(
        ?float $pch = null,
        ?float $pn = null,
        ?float $paux = null,
        ?float $scop = null,
        ?Pourcentage $rpn = null,
        ?Pourcentage $rpint = null,
        ?float $qp0 = null,
        ?float $pveilleuse = null,
        ?float $tfonc30 = null,
        ?float $tfonc100 = null,
        ?Pertes $pertes = null,
        ?Pertes $pertes_recuperables = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        Assert::greaterThanEq($pch, 0);
        Assert::greaterThanEq($pn, 0);
        Assert::greaterThanEq($paux, 0);
        Assert::greaterThanEq($scop, 0);
        Assert::greaterThanEq($qp0, 0);
        Assert::greaterThanEq($pveilleuse, 0);
        Assert::greaterThanEq($tfonc30, 0);
        Assert::greaterThanEq($tfonc100, 0);

        return new self(
            pch: $pch,
            pn: $pn,
            paux: $paux,
            scop: $scop,
            rpn: $rpn,
            rpint: $rpint,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
            tfonc30: $tfonc30,
            tfonc100: $tfonc100,
            pertes: $pertes,
            pertes_recuperables: $pertes_recuperables,
            consommations: $consommations,
            emissions: $emissions,
        );
    }

    public function with(
        ?float $pch = null,
        ?float $pn = null,
        ?float $paux = null,
        ?float $scop = null,
        ?Pourcentage $rpn = null,
        ?Pourcentage $rpint = null,
        ?float $qp0 = null,
        ?float $pveilleuse = null,
        ?float $tfonc30 = null,
        ?float $tfonc100 = null,
        ?Pertes $pertes = null,
        ?Pertes $pertes_recuperables = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        return self::create(
            pch: $pch ?? $this->pch,
            pn: $pn ?? $this->pn,
            paux: $paux ?? $this->paux,
            scop: $scop ?? $this->scop,
            rpn: $rpn ?? $this->rpn,
            rpint: $rpint ?? $this->rpint,
            qp0: $qp0 ?? $this->qp0,
            pveilleuse: $pveilleuse ?? $this->pveilleuse,
            tfonc30: $tfonc30 ?? $this->tfonc30,
            tfonc100: $tfonc100 ?? $this->tfonc100,
            pertes: $pertes ? ($this->pertes?->merge($pertes) ?? $pertes) : $this->pertes,
            pertes_recuperables: $pertes_recuperables ? ($this->pertes_recuperables?->merge($pertes_recuperables) ?? $pertes_recuperables) : $this->pertes_recuperables,
            consommations: $consommations ? ($this->consommations?->merge($consommations) ?? $consommations) : $this->consommations,
            emissions: $emissions ? ($this->emissions?->merge($emissions) ?? $emissions) : $this->emissions,
        );
    }
}
