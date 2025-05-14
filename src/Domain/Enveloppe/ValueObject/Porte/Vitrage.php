<?php

namespace App\Domain\Enveloppe\ValueObject\Porte;

use App\Domain\Common\ValueObject\Pourcentage;
use App\Domain\Enveloppe\Enum\Porte\TypeVitrage;
use Webmozart\Assert\Assert;

final class Vitrage
{
    public function __construct(
        public readonly Pourcentage $taux_vitrage,
        public readonly ?TypeVitrage $type_vitrage,
    ) {}

    public static function create(Pourcentage $taux_vitrage, ?TypeVitrage $type_vitrage,): self
    {
        Assert::greaterThanEq($taux_vitrage->value(), 0);
        Assert::lessThanEq($taux_vitrage->value(), 60);

        return new self(
            taux_vitrage: $taux_vitrage,
            type_vitrage: $taux_vitrage->decimal() > 0 ? $type_vitrage : null,
        );
    }
}
