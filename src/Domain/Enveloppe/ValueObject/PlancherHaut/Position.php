<?php

namespace App\Domain\Enveloppe\ValueObject\PlancherHaut;

use App\Domain\Common\ValueObject\Orientation;
use App\Domain\Enveloppe\Entity\Lnc;
use App\Domain\Enveloppe\Enum\Mitoyennete;
use Webmozart\Assert\Assert;

final class Position
{
    public function __construct(
        public readonly float $surface,
        public readonly Mitoyennete $mitoyennete,
        public readonly ?Orientation $orientation,
        public readonly ?Lnc $local_non_chauffe = null,
    ) {}

    public static function create(
        float $surface,
        Mitoyennete $mitoyennete,
        ?Orientation $orientation,
        ?Lnc $local_non_chauffe,
    ): self {
        Assert::greaterThan($surface, 0);

        if ($mitoyennete === Mitoyennete::LOCAL_NON_CHAUFFE && null === $local_non_chauffe) {
            $mitoyennete = Mitoyennete::LOCAL_NON_ACCESSIBLE;
        }

        return new self(
            surface: $surface,
            mitoyennete: $local_non_chauffe ? Mitoyennete::LOCAL_NON_CHAUFFE : $mitoyennete,
            orientation: $orientation,
            local_non_chauffe: $local_non_chauffe,
        );
    }
}
