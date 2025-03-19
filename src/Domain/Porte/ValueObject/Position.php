<?php

namespace App\Domain\Porte\ValueObject;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Porte\Enum\Mitoyennete;

final class Position
{
    public function __construct(
        public readonly ?Id $paroi_id,
        public readonly ?Id $local_non_chauffe_id,
        public readonly Mitoyennete $mitoyennete,
        public readonly float $surface,
        public readonly ?float $orientation,
    ) {}

    public static function create(
        Mitoyennete $mitoyennete,
        float $surface,
        ?Id $paroi_id = null,
        ?Id $local_non_chauffe_id = null,
        ?float $orientation = null,
    ): self {
        return new self(
            mitoyennete: $mitoyennete,
            surface: $surface,
            paroi_id: $paroi_id,
            local_non_chauffe_id: $local_non_chauffe_id,
            orientation: $orientation,
        );
    }
}
