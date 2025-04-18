<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Chauffage\Enum\UsageChauffage;
use App\Domain\Common\ValueObject\{Annee, Pourcentage};
use Webmozart\Assert\Assert;

final class Solaire
{
    public function __construct(
        public readonly UsageChauffage $usage,
        public readonly ?Annee $annee_installation,
        public readonly ?Pourcentage $fch,
    ) {}

    public static function create(
        UsageChauffage $usage,
        ?Annee $annee_installation,
        ?Pourcentage $fch,
    ): self {
        Assert::nullOrGreaterThanEq($fch?->value(), 0);
        Assert::nullOrLessThanEq($fch?->value(), 100);
        return new self(usage: $usage, annee_installation: $annee_installation, fch: $fch,);
    }
}
