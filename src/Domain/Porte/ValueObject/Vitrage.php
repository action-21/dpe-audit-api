<?php

namespace App\Domain\Porte\ValueObject;

use App\Domain\Porte\Enum\TypeVitrage;
use Webmozart\Assert\Assert;

final class Vitrage
{
    public function __construct(
        public readonly int $taux_vitrage,
        public readonly ?TypeVitrage $type_vitrage,
    ) {}

    public static function create(int $taux_vitrage, TypeVitrage $type_vitrage,): self
    {
        $value = new self(taux_vitrage: $taux_vitrage, type_vitrage: $taux_vitrage > 0 ? $type_vitrage : null);
        $value->controle();
        return $value;
    }

    public function controle(): void
    {
        Assert::greaterThanEq($this->taux_vitrage, 0);
        Assert::lessThanEq($this->taux_vitrage, 60);
    }
}
