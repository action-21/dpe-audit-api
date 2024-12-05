<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\Type\Id;
use App\Domain\Ecs\Enum\{CategorieGenerateur, EnergieGenerateur, LabelGenerateur, TypeChaudiere, TypeGenerateur};
use Webmozart\Assert\Assert;

abstract class Signaletique
{
    public function __construct(
        public readonly TypeGenerateur $type,
        public readonly EnergieGenerateur $energie,
        public readonly int $volume_stockage,
        public readonly bool $position_volume_chauffe,
        public readonly bool $generateur_collectif,
        public readonly ?TypeChaudiere $type_chaudiere = null,
        public readonly ?LabelGenerateur $label = null,
        public readonly ?bool $presence_ventouse = null,
        public readonly ?float $pn = null,
        public readonly ?float $rpn = null,
        public readonly ?float $qp0 = null,
        public readonly ?float $pveilleuse = null,
        public readonly ?float $cop = null,
    ) {}

    protected function controle(): void
    {
        Assert::greaterThanEq($this->volume_stockage, 0);
        Assert::nullOrGreaterThan($this->pn, 0);
        Assert::nullOrGreaterThan($this->rpn, 0);
        Assert::nullOrGreaterThanEq($this->qp0, 0);
        Assert::nullOrGreaterThanEq($this->pveilleuse, 0);
        Assert::nullOrGreaterThanEq($this->cop, 0);
    }

    public function categorie(): CategorieGenerateur
    {
        return CategorieGenerateur::determine($this->type, $this->energie);
    }
}
