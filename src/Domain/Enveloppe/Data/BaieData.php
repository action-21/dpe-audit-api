<?php

namespace App\Domain\Enveloppe\Data;

use App\Domain\Enveloppe\Enum\{EtatIsolation, Performance};
use App\Domain\Enveloppe\ValueObject\Baie\Ensoleillements;
use Webmozart\Assert\Assert;

final class BaieData
{
    public function __construct(
        public readonly ?float $sdep,
        public readonly ?float $ug,
        public readonly ?float $uw,
        public readonly ?float $u,
        public readonly ?float $b,
        public readonly ?float $dp,
        public readonly ?Performance $performance,
        public readonly ?EtatIsolation $isolation,
        public readonly ?Ensoleillements $ensoleillements,
    ) {}

    public static function create(
        ?float $sdep = null,
        ?float $ug = null,
        ?float $uw = null,
        ?float $u = null,
        ?float $b = null,
        ?float $dp = null,
        ?Performance $performance = null,
        ?EtatIsolation $isolation = null,
        ?Ensoleillements $ensoleillements = null,
    ): self {
        Assert::nullOrGreaterThanEq($sdep, 0);
        Assert::nullOrGreaterThanEq($ug, 0);
        Assert::nullOrGreaterThanEq($uw, 0);
        Assert::nullOrGreaterThanEq($u, 0);
        Assert::nullOrGreaterThanEq($b, 0);
        Assert::nullOrGreaterThanEq($dp, 0);

        return new self(
            sdep: $sdep,
            ug: $ug,
            uw: $uw,
            u: $u,
            b: $b,
            dp: $dp,
            performance: $performance,
            isolation: $isolation,
            ensoleillements: $ensoleillements,
        );
    }

    public function with(
        ?float $sdep = null,
        ?float $ug = null,
        ?float $uw = null,
        ?float $u = null,
        ?float $b = null,
        ?float $dp = null,
        ?Performance $performance = null,
        ?EtatIsolation $isolation = null,
        ?Ensoleillements $ensoleillements = null,
    ): self {
        return self::create(
            sdep: $sdep ?? $this->sdep,
            ug: $ug ?? $this->ug,
            uw: $uw ?? $this->uw,
            u: $u ?? $this->u,
            b: $b ?? $this->b,
            dp: $dp ?? $this->dp,
            performance: $performance ?? $this->performance,
            isolation: $isolation ?? $this->isolation,
            ensoleillements: $ensoleillements ?? $this->ensoleillements,
        );
    }
}
