<?php

namespace App\Domain\Ecs\ValueObject\Generateur;

use App\Domain\Ecs\Enum\{LabelGenerateur, TypeChaudiere};
use Webmozart\Assert\Assert;

final class Signaletique
{
    public function __construct(
        public readonly float $volume_stockage,
        public readonly ?TypeChaudiere $type_chaudiere,
        public readonly ?LabelGenerateur $label,
        public readonly ?float $pn,
        public readonly ?float $cop,
        public readonly ?Combustion $combustion,
    ) {}

    public static function create(
        float $volume_stockage,
        ?TypeChaudiere $type_chaudiere = null,
        ?LabelGenerateur $label = null,
        ?float $pn = null,
        ?float $cop = null,
        ?Combustion $combustion = null,
    ): self {
        Assert::greaterThanEq($volume_stockage, 0);
        Assert::nullOrGreaterThan($pn, 0);
        Assert::nullOrGreaterThan($cop, 0);

        return new self(
            volume_stockage: $volume_stockage,
            type_chaudiere: $type_chaudiere,
            label: $label,
            pn: $pn,
            cop: $cop,
            combustion: $combustion,
        );
    }
}
