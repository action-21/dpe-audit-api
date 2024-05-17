<?php

namespace App\Domain\Refend;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Refend\ValueObject\Dimensions;

final class Refend
{
    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private bool $refend_lourd,
        private Dimensions $dimensions,
    ) {
    }

    public static function create(
        Enveloppe $enveloppe,
        string $description,
        bool $refend_lourd,
        Dimensions $dimensions,
    ): self {
        return new self(
            id: Id::create(),
            enveloppe: $enveloppe,
            description: $description,
            refend_lourd: $refend_lourd,
            dimensions: $dimensions,
        );
    }

    public function update(string $description, bool $refend_lourd, Dimensions $dimensions): self
    {
        $this->description = $description;
        $this->refend_lourd = $refend_lourd;
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

    public function refend_lourd(): bool
    {
        return $this->refend_lourd;
    }

    public function dimensions(): Dimensions
    {
        return $this->dimensions;
    }
}
