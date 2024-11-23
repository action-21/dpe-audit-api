<?php

namespace App\Domain\Lnc\ValueObject;

use App\Domain\Common\Service\Assert;
use App\Domain\Lnc\Entity\Paroi;
use App\Domain\Lnc\Enum\Mitoyennete;

final class Position
{
    public function __construct(
        public readonly ?float $orientation = null,
        public readonly ?Paroi $paroi = null,
        public readonly ?Mitoyennete $mitoyennete = null,
    ) {}

    public static function create(Mitoyennete $mitoyennete, ?float $orientation,): self
    {
        return new self(mitoyennete: $mitoyennete, orientation: $orientation);
    }

    public static function create_liaison_paroi(Paroi $entity, ?float $orientation,): self
    {
        return new self(paroi: $entity, orientation: $orientation);
    }

    public function controle(): void
    {
        Assert::orientation($this->orientation);
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->mitoyennete ?? $this->paroi->position()->mitoyennete();
    }
}
