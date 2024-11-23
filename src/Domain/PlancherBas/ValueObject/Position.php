<?php

namespace App\Domain\PlancherBas\ValueObject;

use App\Domain\Common\Type\Id;
use App\Domain\PlancherBas\Enum\Mitoyennete;

final class Position
{
    public function __construct(
        public readonly Mitoyennete $mitoyennete,
        public readonly ?Id $local_non_chauffe_id = null,
    ) {}

    public static function create(Mitoyennete $mitoyennete): self
    {
        return new self(mitoyennete: $mitoyennete);
    }

    public static function create_liaison_local_non_chauffe(Id $local_non_chauffe_id): self
    {
        return new self(local_non_chauffe_id: $local_non_chauffe_id, mitoyennete: Mitoyennete::LOCAL_NON_CHAUFFE);
    }

    public function controle(): void
    {
        if ($this->mitoyennete->local_non_chauffe() && $this->local_non_chauffe_id === null)
            throw new \InvalidArgumentException('Le local non chauffé est requis');
    }
}
