<?php

namespace App\Domain\PlancherHaut\ValueObject;

use App\Domain\Common\Type\Id;
use App\Domain\PlancherHaut\Enum\Mitoyennete;
use Webmozart\Assert\Assert;

final class Position
{
    public function __construct(
        public readonly Mitoyennete $mitoyennete,
        public readonly ?Id $local_non_chauffe_id,
        public readonly ?float $orientation,
    ) {}

    public static function create(Mitoyennete $mitoyennete, ?float $orientation, ?Id $local_non_chauffe_id): self
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
