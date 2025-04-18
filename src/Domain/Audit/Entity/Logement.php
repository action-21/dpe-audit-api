<?php

namespace App\Domain\Audit\Entity;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Enum\Typologie;
use App\Domain\Common\ValueObject\Id;
use Webmozart\Assert\Assert;

final class Logement
{
    public function __construct(
        private readonly Id $id,
        private readonly Audit $audit,
        private string $description,
        private Typologie $typologie,
        private float $surface_habitable,
    ) {}

    public static function create(
        Id $id,
        Audit $audit,
        string $description,
        Typologie $typologie,
        float $surface_habitable,
    ): self {
        Assert::greaterThan($surface_habitable, 0);

        return new self(
            id: $id,
            audit: $audit,
            description: $description,
            typologie: $typologie,
            surface_habitable: $surface_habitable,
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

    public function typologie(): Typologie
    {
        return $this->typologie;
    }

    public function surface_habitable(): float
    {
        return $this->surface_habitable;
    }
}
