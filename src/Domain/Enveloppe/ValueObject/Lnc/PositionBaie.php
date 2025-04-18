<?php

namespace App\Domain\Enveloppe\ValueObject\Lnc;

use App\Domain\Common\ValueObject\{Inclinaison, Orientation};
use App\Domain\Enveloppe\Enum\Mitoyennete;
use App\Domain\Enveloppe\Entity\Lnc\ParoiOpaque;
use Webmozart\Assert\Assert;

final class PositionBaie
{
    public function __construct(
        public readonly Mitoyennete $mitoyennete,
        public readonly float $surface,
        public readonly Inclinaison $inclinaison,
        public readonly ?Orientation $orientation,
        public readonly ?ParoiOpaque $paroi = null,
    ) {}

    public static function create(
        Mitoyennete $mitoyennete,
        float $surface,
        Inclinaison $inclinaison,
        ?Orientation $orientation = null,
        ?ParoiOpaque $paroi = null,
    ): self {
        Assert::greaterThan($surface, 0);

        $value = new self(
            mitoyennete: $mitoyennete,
            surface: $surface,
            inclinaison: $inclinaison,
            orientation: $orientation,
        );

        return $paroi ? $value->with_paroi($paroi) : $value;
    }

    public function with_paroi(ParoiOpaque $paroi): self
    {
        return new self(
            surface: $this->surface,
            mitoyennete: $paroi->position()->mitoyennete,
            orientation: $this->orientation,
            inclinaison: $this->inclinaison,
            paroi: $paroi,
        );
    }
}
