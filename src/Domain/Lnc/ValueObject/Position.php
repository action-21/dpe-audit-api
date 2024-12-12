<?php

namespace App\Domain\Lnc\ValueObject;

use App\Domain\Common\Type\Id;
use App\Domain\Lnc\Entity\Paroi;
use App\Domain\Lnc\Enum\Mitoyennete;
use Webmozart\Assert\Assert;

final class Position
{
    public function __construct(
        public readonly ?float $orientation = null,
        public readonly ?Id $paroi_id = null,
        public readonly ?Mitoyennete $mitoyennete = null,
    ) {}

    public static function create(Mitoyennete $mitoyennete, ?float $orientation,): self
    {
        Assert::nullOrGreaterThanEq($orientation, 0);
        Assert::nullOrLessThan($orientation, 360);
        return new self(mitoyennete: $mitoyennete, orientation: $orientation);
    }

    public static function create_liaison_paroi(Id $paroi_id, ?float $orientation,): self
    {
        Assert::nullOrGreaterThanEq($orientation, 0);
        Assert::nullOrLessThan($orientation, 360);
        return new self(paroi_id: $paroi_id, orientation: $orientation);
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->mitoyennete ?? $this->paroi->position()->mitoyennete();
    }
}
