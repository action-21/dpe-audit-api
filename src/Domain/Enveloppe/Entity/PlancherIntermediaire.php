<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\Inertie;
use App\Domain\Enveloppe\Enveloppe;
use Webmozart\Assert\Assert;

final class PlancherIntermediaire
{
    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private float $surface,
        private Inertie $inertie,
    ) {}

    public static function create(
        Enveloppe $enveloppe,
        string $description,
        float $surface,
        Inertie\InertiePlancherIntermediaire $inertie,
    ): self {
        Assert::greaterThan($surface, 0);
        return new self(
            id: Id::create(),
            enveloppe: $enveloppe,
            description: $description,
            surface: $surface,
            inertie: $inertie->to(),
        );
    }

    public function controle(): void
    {
        Assert::greaterThan($this->surface, 0);
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

    public function surface(): float
    {
        return $this->surface;
    }

    public function inertie(): Inertie
    {
        return $this->inertie;
    }

    public function est_lourd(): bool
    {
        return $this->inertie->est_lourd();
    }
}
