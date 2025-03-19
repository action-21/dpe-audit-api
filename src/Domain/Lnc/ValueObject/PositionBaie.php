<?php

namespace App\Domain\Lnc\ValueObject;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Lnc\Enum\Mitoyennete;
use Webmozart\Assert\Assert;

final class PositionBaie
{
    public function __construct(
        public readonly Mitoyennete $mitoyennete,
        public readonly float $surface,
        public readonly float $inclinaison,
        public readonly ?float $orientation,
        public readonly ?Id $paroi_id,
    ) {}

    public static function create(
        Mitoyennete $mitoyennete,
        float $surface,
        float $inclinaison,
        ?float $orientation = null,
        ?Id $paroi_id = null,
    ): self {
        $value = new self(
            mitoyennete: $mitoyennete,
            surface: $surface,
            inclinaison: $inclinaison,
            orientation: $orientation,
            paroi_id: $paroi_id,
        );
        $value->controle();
        return $value;
    }

    public function controle(): void
    {
        Assert::greaterThan($this->surface, 0);
        Assert::greaterThanEq($this->inclinaison, 0);
        Assert::lessThanEq($this->inclinaison, 90);
        Assert::nullOrGreaterThanEq($this->orientation, 0);
        Assert::nullOrLessThan($this->orientation, 360);
    }
}
