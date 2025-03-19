<?php

namespace App\Domain\Mur\ValueObject;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Mur\Enum\Mitoyennete;
use Webmozart\Assert\Assert;

final class Position
{
    public function __construct(
        public readonly Mitoyennete $mitoyennete,
        public readonly float $orientation,
        public readonly ?Id $local_non_chauffe_id,
    ) {}

    public static function create(Mitoyennete $mitoyennete, float $orientation, ?Id $local_non_chauffe_id): self
    {
        Assert::greaterThanEq($orientation, 0);
        Assert::lessThan($orientation, 360);

        if ($local_non_chauffe_id) {
            $mitoyennete = Mitoyennete::LOCAL_NON_CHAUFFE;
        }
        if ($mitoyennete === Mitoyennete::LOCAL_NON_CHAUFFE && null === $local_non_chauffe_id) {
            $mitoyennete = Mitoyennete::LOCAL_NON_ACCESSIBLE;
        }
        return new self(
            mitoyennete: $mitoyennete,
            orientation: $orientation,
            local_non_chauffe_id: $local_non_chauffe_id,
        );
    }
}
