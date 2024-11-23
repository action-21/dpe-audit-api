<?php

namespace App\Domain\Enveloppe\ValueObject;

use App\Domain\Enveloppe\Enum\Inertie as InertieEnum;

final class Inertie
{
    public function __construct(
        public readonly InertieEnum $inertie,
        public readonly bool $paroi_ancienne,
    ) {}

    public static function create(InertieEnum $inertie, bool $paroi_ancienne): self
    {
        return new self(inertie: $inertie, paroi_ancienne: $paroi_ancienne);
    }
}
