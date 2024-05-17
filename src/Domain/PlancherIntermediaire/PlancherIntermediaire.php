<?php

namespace App\Domain\PlancherIntermediaire;

use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Common\ValueObject\Id;
use App\Domain\PlancherIntermediaire\ValueObject\Dimensions;

final class PlancherIntermediaire
{
    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private bool $plancher_haut_lourd,
        private bool $plancher_bas_lourd,
        private Dimensions $dimensions,
    ) {
    }

    public static function create(
        Enveloppe $enveloppe,
        string $description,
        bool $plancher_haut_lourd,
        bool $plancher_bas_lourd,
        Dimensions $dimensions,
    ): self {
        return new self(
            id: Id::create(),
            enveloppe: $enveloppe,
            description: $description,
            plancher_haut_lourd: $plancher_haut_lourd,
            plancher_bas_lourd: $plancher_bas_lourd,
            dimensions: $dimensions,
        );
    }

    public function update(
        string $description,
        bool $plancher_haut_lourd,
        bool $plancher_bas_lourd,
        Dimensions $dimensions,
    ): self {
        $this->description = $description;
        $this->plancher_haut_lourd = $plancher_haut_lourd;
        $this->plancher_bas_lourd = $plancher_bas_lourd;
        $this->dimensions = $dimensions;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function enveloppe(): Enveloppe
    {
        return $this->enveloppe;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function plancher_haut_lourd(): bool
    {
        return $this->plancher_haut_lourd;
    }

    public function plancher_bas_lourd(): bool
    {
        return $this->plancher_bas_lourd;
    }

    public function dimensions(): Dimensions
    {
        return $this->dimensions;
    }
}
