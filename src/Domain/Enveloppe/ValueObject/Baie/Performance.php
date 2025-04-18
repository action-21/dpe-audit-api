<?php

namespace App\Domain\Enveloppe\ValueObject\Baie;

use App\Domain\Common\ValueObject\Pourcentage;
use Webmozart\Assert\Assert;

final class Performance
{
    public function __construct(
        public readonly ?float $ug,
        public readonly ?float $uw,
        public readonly ?float $ujn,
        public readonly ?Pourcentage $sw,
    ) {}

    public static function create(
        ?float $ug = null,
        ?float $uw = null,
        ?float $ujn = null,
        ?Pourcentage $sw = null,
    ): self {
        Assert::nullOrGreaterThan($ug, 0);
        Assert::nullOrGreaterThanEq($uw, 0);
        Assert::nullOrGreaterThanEq($ujn, 0);
        Assert::nullOrGreaterThanEq($sw?->value(), 0);
        Assert::nullOrLessThanEq($sw?->value(), 100);

        return new self(ug: $ug, uw: $uw, ujn: $ujn, sw: $sw);
    }
}
