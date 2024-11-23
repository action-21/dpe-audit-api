<?php

namespace App\Domain\Visite\Entity;

use App\Domain\Common\Service\Assert;
use App\Domain\Common\Type\Id;
use App\Domain\Visite\Enum\Typologie;
use App\Domain\Visite\Visite;

final class Logement
{
    public function __construct(
        private readonly Id $id,
        private readonly Visite $visite,
        private string $description,
        private Typologie $typologie,
        private float $surface_habitable,
    ) {}

    public function update(string $description, Typologie $typologie, float $surface_habitable): self
    {
        $this->description = $description;
        $this->surface_habitable = $surface_habitable;
        $this->typologie = $typologie;
        return $this;
    }

    public function controle(): void
    {
        Assert::positif($this->surface_habitable);
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function visite(): Visite
    {
        return $this->visite;
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
