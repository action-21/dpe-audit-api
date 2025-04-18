<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Data\NiveauData;
use App\Domain\Enveloppe\Enum\Inertie;
use App\Domain\Enveloppe\Enveloppe;
use Webmozart\Assert\Assert;

final class Niveau
{
    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private float $surface,
        private Inertie $inertie_paroi_verticale,
        private Inertie $inertie_plancher_haut,
        private Inertie $inertie_plancher_bas,
        private NiveauData $data,
    ) {}

    public static function create(
        Enveloppe $enveloppe,
        float $surface,
        Inertie $inertie_paroi_verticale,
        Inertie $inertie_plancher_haut,
        Inertie $inertie_plancher_bas,
    ): self {
        Assert::greaterThan($surface, 0);
        return new self(
            id: Id::create(),
            enveloppe: $enveloppe,
            surface: $surface,
            inertie_paroi_verticale: $inertie_paroi_verticale,
            inertie_plancher_haut: $inertie_plancher_haut,
            inertie_plancher_bas: $inertie_plancher_bas,
            data: NiveauData::create(),
        );
    }

    public function reinitialise(): void
    {
        $this->data = NiveauData::create();
    }

    public function calcule(NiveauData $data): self
    {
        $this->data = $data;
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

    public function surface(): float
    {
        return $this->surface;
    }

    public function inertie_paroi_verticale(): Inertie
    {
        return $this->inertie_paroi_verticale;
    }

    public function inertie_plancher_haut(): Inertie
    {
        return $this->inertie_plancher_haut;
    }

    public function inertie_plancher_bas(): Inertie
    {
        return $this->inertie_plancher_bas;
    }

    public function data(): NiveauData
    {
        return $this->data;
    }
}
