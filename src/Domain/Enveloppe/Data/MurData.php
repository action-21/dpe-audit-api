<?php

namespace App\Domain\Enveloppe\Data;

use App\Domain\Enveloppe\Enum\{EtatIsolation, Performance};
use Webmozart\Assert\Assert;

final class MurData
{
    public function __construct(
        public readonly ?float $sdep,
        public readonly ?float $b,
        public readonly ?float $u0,
        public readonly ?float $u,
        public readonly ?float $dp,
        public readonly ?EtatIsolation $isolation,
        public readonly ?Performance $performance,
    ) {}

    public static function create(
        ?float $sdep = null,
        ?float $u0 = null,
        ?float $u = null,
        ?float $b = null,
        ?float $dp = null,
        ?EtatIsolation $isolation = null,
        ?Performance $performance = null,
    ): self {
        Assert::nullOrGreaterThanEq($sdep, 0);
        Assert::nullOrGreaterThanEq($u0, 0);
        Assert::nullOrGreaterThanEq($u, 0);
        Assert::nullOrGreaterThanEq($b, 0);
        Assert::nullOrGreaterThanEq($dp, 0);

        return new self(
            sdep: $sdep,
            u: $u,
            u0: $u0,
            b: $b,
            dp: $dp,
            isolation: $isolation,
            performance: $performance,
        );
    }

    public function with(
        ?float $sdep = null,
        ?float $u0 = null,
        ?float $u = null,
        ?float $b = null,
        ?float $dp = null,
        ?EtatIsolation $isolation = null,
        ?Performance $performance = null,
    ): self {
        return self::create(
            sdep: $sdep ?? $this->sdep,
            u: $u ?? $this->u,
            u0: $u0 ?? $this->u0,
            b: $b ?? $this->b,
            dp: $dp ?? $this->dp,
            isolation: $isolation ?? $this->isolation,
            performance: $performance ?? $this->performance,
        );
    }
}
