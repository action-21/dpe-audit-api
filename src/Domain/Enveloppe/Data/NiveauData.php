<?php

namespace App\Domain\Enveloppe\Data;

use App\Domain\Enveloppe\Enum\Inertie;

final class NiveauData
{
    public function __construct(public readonly ?Inertie $inertie) {}

    public static function create(?Inertie $inertie = null,): self
    {
        return new self(
            inertie: $inertie,
        );
    }

    public function with(?Inertie $inertie = null,): self
    {
        return self::create(
            inertie: $inertie ?? $this->inertie,
        );
    }
}
