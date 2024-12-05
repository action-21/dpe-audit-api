<?php

namespace App\Domain\PlancherBas\ValueObject;

use App\Domain\Common\Type\Id;
use App\Domain\PlancherBas\Enum\Mitoyennete;

final class Position
{
    public function __construct(
        public readonly Mitoyennete $mitoyennete,
        public readonly ?Id $local_non_chauffe_id,
    ) {}

    public static function create(Mitoyennete $mitoyennete, ?Id $local_non_chauffe_id): self
    {
        if ($local_non_chauffe_id) {
            $mitoyennete = Mitoyennete::LOCAL_NON_CHAUFFE;
        }
        if ($mitoyennete === Mitoyennete::LOCAL_NON_CHAUFFE && null === $local_non_chauffe_id) {
            $mitoyennete = Mitoyennete::LOCAL_NON_ACCESSIBLE;
        }
        return new self(
            mitoyennete: $mitoyennete,
            local_non_chauffe_id: $local_non_chauffe_id,
        );
    }
}
