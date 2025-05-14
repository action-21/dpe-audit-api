<?php

namespace App\Domain\Enveloppe\Data\Lnc;

use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\ValueObject\Lnc\EnsoleillementsBaie;
use Webmozart\Assert\Assert;

final class BaieData
{
    public function __construct(
        public readonly ?float $aue,
        public readonly ?float $aiu,
        public readonly ?EtatIsolation $isolation,
        public readonly ?EnsoleillementsBaie $ensoleillements,
    ) {}

    public static function create(
        ?float $aue = null,
        ?float $aiu = null,
        ?EtatIsolation $isolation = null,
        ?EnsoleillementsBaie $ensoleillements = null,
    ): self {
        Assert::nullOrGreaterThanEq($aue, 0);
        Assert::nullOrGreaterThanEq($aiu, 0);

        return new self(
            aue: $aue,
            aiu: $aiu,
            isolation: $isolation,
            ensoleillements: $ensoleillements,
        );
    }

    public function with(
        ?float $aue = null,
        ?float $aiu = null,
        ?EtatIsolation $isolation = null,
        ?EnsoleillementsBaie $ensoleillements = null,
    ): self {
        return self::create(
            aue: $aue ?? $this->aue,
            aiu: $aiu ?? $this->aiu,
            isolation: $isolation ?? $this->isolation,
            ensoleillements: $ensoleillements ?? $this->ensoleillements,
        );
    }
}
