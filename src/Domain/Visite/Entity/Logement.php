<?php

namespace App\Domain\Visite\Entity;

use App\Domain\Common\Type\Id;
use App\Domain\Visite\Enum\Typologie;
use App\Domain\Visite\Visite;
use Webmozart\Assert\Assert;

final class Logement
{
    public function __construct(
        private readonly Id $id,
        private readonly Visite $visite,
        private string $description,
        private Typologie $typologie,
        private float $surface_habitable,
    ) {}

    public function controle(): void
    {
        Assert::greaterThan($this->surface_habitable, 0);
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
