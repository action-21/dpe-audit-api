<?php

namespace App\Domain\Enveloppe\Data;

use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\ValueObject\Lnc\Ensoleillements;
use Webmozart\Assert\Assert;

final class LncData
{
    public function __construct(
        public readonly ?float $aiu,
        public readonly ?float $aue,
        public readonly ?EtatIsolation $isolation_aiu,
        public readonly ?EtatIsolation $isolation_aue,
        public readonly ?float $uvue,
        public readonly ?float $b,
        public readonly ?float $bver,
        public readonly ?Ensoleillements $ensoleillements,
    ) {}

    public static function create(
        ?float $aue = null,
        ?float $aiu = null,
        ?EtatIsolation $isolation_aiu = null,
        ?EtatIsolation $isolation_aue = null,
        ?float $uvue = null,
        ?float $b = null,
        ?float $bver = null,
        ?Ensoleillements $ensoleillements = null,
    ): self {
        Assert::nullOrGreaterThanEq($aue, 0);
        Assert::nullOrGreaterThanEq($aiu, 0);
        Assert::nullOrGreaterThanEq($uvue, 0);
        Assert::nullOrGreaterThanEq($b, 0);
        Assert::nullOrGreaterThanEq($bver, 0);

        return new self(
            aue: $aue,
            aiu: $aiu,
            isolation_aiu: $isolation_aiu,
            isolation_aue: $isolation_aue,
            uvue: $uvue,
            b: $b,
            bver: $bver,
            ensoleillements: $ensoleillements,
        );
    }

    public function with(
        ?float $aue = null,
        ?float $aiu = null,
        ?EtatIsolation $isolation_aiu = null,
        ?EtatIsolation $isolation_aue = null,
        ?float $uvue = null,
        ?float $b = null,
        ?float $bver = null,
        ?Ensoleillements $ensoleillements = null,
    ): self {
        return self::create(
            aue: $aue ?? $this->aue,
            aiu: $aiu ?? $this->aiu,
            isolation_aiu: $isolation_aiu ?? $this->isolation_aiu,
            isolation_aue: $isolation_aue ?? $this->isolation_aue,
            uvue: $uvue ?? $this->uvue,
            b: $b ?? $this->b,
            bver: $bver ?? $this->bver,
            ensoleillements: $ensoleillements ?? $this->ensoleillements,
        );
    }
}
