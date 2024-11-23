<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\Service\Assert;
use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enum\Inertie;
use App\Domain\Enveloppe\Enum\Inertie\InertieRefend;
use App\Domain\Enveloppe\Enveloppe;

final class Refend
{
    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private float $surface,
        private Inertie $inertie,
    ) {}

    public function update(string $description, float $surface, InertieRefend $inertie,): self
    {
        $this->description = $description;
        $this->surface = $surface;
        $this->inertie = $inertie->to();
        $this->controle();
        return $this;
    }

    public function controle(): void
    {
        Assert::positif($this->surface);
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
