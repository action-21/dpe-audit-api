<?php

namespace App\Domain\PlancherBas\ValueObject;

use App\Domain\Common\Type\Id;
use App\Domain\PlancherBas\Enum\Mitoyennete;
use Webmozart\Assert\Assert;

final class Position
{
    public function __construct(
        public readonly Mitoyennete $mitoyennete,
        public readonly ?Id $local_non_chauffe_id = null,
    ) {}

    public static function create(Mitoyennete $mitoyennete): self
    {
        Assert::notEq($mitoyennete, Mitoyennete::LOCAL_NON_CHAUFFE);
        return new self(mitoyennete: $mitoyennete);
    }

    public static function create_liaison_local_non_chauffe(Id $local_non_chauffe_id): self
    {
        return new self(local_non_chauffe_id: $local_non_chauffe_id, mitoyennete: Mitoyennete::LOCAL_NON_CHAUFFE);
    }
}
