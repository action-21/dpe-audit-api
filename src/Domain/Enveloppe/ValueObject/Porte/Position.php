<?php

namespace App\Domain\Enveloppe\ValueObject\Porte;

use App\Domain\Common\ValueObject\Orientation;
use App\Domain\Enveloppe\Entity\{Lnc, Paroi};
use App\Domain\Enveloppe\Enum\{Mitoyennete, TypeParoi};
use Webmozart\Assert\Assert;

final class Position
{
    public function __construct(
        public readonly float $surface,
        public readonly Mitoyennete $mitoyennete,
        public readonly ?Orientation $orientation,
        public readonly ?Paroi $paroi = null,
        public readonly ?Lnc $local_non_chauffe = null,
    ) {}

    public static function create(
        float $surface,
        Mitoyennete $mitoyennete,
        ?Orientation $orientation,
        ?Paroi $paroi,
        ?Lnc $local_non_chauffe,
    ): self {
        Assert::greaterThan($surface, 0);
        Assert::nullOrOneOf($paroi?->type_paroi(), TypeParoi::parois_opaques());

        if ($mitoyennete === Mitoyennete::LOCAL_NON_CHAUFFE && null === $local_non_chauffe) {
            $mitoyennete = Mitoyennete::LOCAL_NON_ACCESSIBLE;
        }

        return new self(
            surface: $surface,
            mitoyennete: $local_non_chauffe ? Mitoyennete::LOCAL_NON_CHAUFFE : $mitoyennete,
            orientation: $orientation ?? $paroi?->orientation(),
            paroi: $paroi,
            local_non_chauffe: $local_non_chauffe,
        );
    }
}
