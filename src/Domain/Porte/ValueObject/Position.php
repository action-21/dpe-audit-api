<?php

namespace App\Domain\Porte\ValueObject;

use App\Domain\Common\Type\Id;
use App\Domain\Porte\Enum\Mitoyennete;
use Webmozart\Assert\Assert;

final class Position
{
    public function __construct(
        public readonly ?Id $paroi_id = null,
        public readonly ?Id $local_non_chauffe_id = null,
        public readonly ?Mitoyennete $mitoyennete = null,
        public readonly ?float $orientation = null,
    ) {}

    public static function create(
        Mitoyennete $mitoyennete,
        ?float $orientation,
        ?Id $local_non_chauffe_id,
    ): self {
        Assert::nullOrGreaterThanEq($orientation, 0);
        Assert::nullOrlessThan($orientation, 360);

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

    public static function create_liaison_paroi(Id $paroi_id): self
    {
        return new self(paroi_id: $paroi_id);
    }
}
