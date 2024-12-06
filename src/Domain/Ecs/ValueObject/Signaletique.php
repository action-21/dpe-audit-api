<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Ecs\Enum\{EnergieGenerateur, LabelGenerateur, PositionChaudiere, TypeGenerateur};
use Webmozart\Assert\Assert;

abstract class Signaletique
{
    public function __construct(
        public readonly TypeGenerateur $type,
        public readonly EnergieGenerateur $energie,
        public readonly int $volume_stockage,
        public readonly bool $position_volume_chauffe,
        public readonly bool $generateur_collectif,
        public readonly ?PositionChaudiere $position_chaudiere = null,
        public readonly ?LabelGenerateur $label = null,
        public readonly ?float $pn = null,
        public readonly ?float $cop = null,
        public readonly ?Combustion $combustion = null,
    ) {}

    protected function controle(): void
    {
        Assert::greaterThanEq($this->volume_stockage, 0);
        Assert::nullOrGreaterThan($this->pn, 0);
        Assert::nullOrGreaterThanEq($this->cop, 0);
    }
}
