<?php

namespace App\Domain\Audit\Entity;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Enum\PositionLogement;
use App\Domain\Audit\Enum\Typologie;
use App\Domain\Common\ValueObject\Id;
use Webmozart\Assert\Assert;

final class Logement
{
    public function __construct(
        private readonly Id $id,
        private readonly Audit $audit,
        private string $description,
        private PositionLogement $position,
        private Typologie $typologie,
        private float $surface_habitable,
        private float $hauteur_sous_plafond,
    ) {}

    public static function create(
        Id $id,
        Audit $audit,
        string $description,
        PositionLogement $position,
        Typologie $typologie,
        float $surface_habitable,
        float $hauteur_sous_plafond,
    ): self {
        Assert::greaterThan($surface_habitable, 0);
        Assert::greaterThan($hauteur_sous_plafond, 0);

        return new self(
            id: $id,
            audit: $audit,
            description: $description,
            position: $position,
            typologie: $typologie,
            surface_habitable: $surface_habitable,
            hauteur_sous_plafond: $hauteur_sous_plafond,
        );
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function audit(): Audit
    {
        return $this->audit;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function position(): PositionLogement
    {
        return $this->position;
    }

    public function typologie(): Typologie
    {
        return $this->typologie;
    }

    public function surface_habitable(): float
    {
        return $this->surface_habitable;
    }

    public function hauteur_sous_plafond(): float
    {
        return $this->hauteur_sous_plafond;
    }
}
