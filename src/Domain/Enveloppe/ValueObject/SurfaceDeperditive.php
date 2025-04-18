<?php

namespace App\Domain\Enveloppe\ValueObject;

use App\Domain\Enveloppe\Enum\{EtatIsolation, TypeParoi};
use Webmozart\Assert\Assert;

final class SurfaceDeperditive
{
    public function __construct(
        public readonly TypeParoi $type,
        public readonly EtatIsolation $isolation,
        public readonly float $sdep,
    ) {}

    public static function create(TypeParoi $type, EtatIsolation $isolation, float $sdep): self
    {
        Assert::greaterThanEq($sdep, 0);
        return new self(type: $type, isolation: $isolation, sdep: $sdep);
    }
}
