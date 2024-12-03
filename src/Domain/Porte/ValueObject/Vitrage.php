<?php

namespace App\Domain\Porte\ValueObject;

use App\Domain\Porte\Enum\TypeVitrage;
use Webmozart\Assert\Assert;

final class Vitrage
{
    public function __construct(
        public readonly int $taux_vitrage = 0,
        public readonly ?TypeVitrage $type_vitrage = null,
    ) {}

    public static function create(int $taux_vitrage, TypeVitrage $type_vitrage,): self
    {
        Assert::greaterThanEq($taux_vitrage, 0);
        Assert::lessThanEq($taux_vitrage, 60);

        return new self(taux_vitrage: $taux_vitrage, type_vitrage: $taux_vitrage > 0 ? $type_vitrage : null);
    }
}
