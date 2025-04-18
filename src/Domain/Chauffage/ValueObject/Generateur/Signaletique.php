<?php

namespace App\Domain\Chauffage\ValueObject\Generateur;

use App\Domain\Chauffage\Enum\{EnergieGenerateur, LabelGenerateur, TypeChaudiere, TypeGenerateur};
use Webmozart\Assert\Assert;

final class Signaletique
{
    public function __construct(
        public readonly ?TypeChaudiere $type_chaudiere,
        public readonly ?float $pn,
        public readonly ?float $scop,
        public readonly ?LabelGenerateur $label,
        public readonly ?int $priorite_cascade,
        public readonly ?Combustion $combustion,
    ) {}

    public static function create(
        ?TypeChaudiere $type_chaudiere = null,
        ?float $pn = null,
        ?float $scop = null,
        ?LabelGenerateur $label = null,
        ?int $priorite_cascade = null,
        ?Combustion $combustion = null,
    ): self {
        Assert::nullOrGreaterThan($priorite_cascade, 0);
        Assert::nullOrGreaterThan($pn, 0);
        Assert::nullOrGreaterThan($scop, 0);

        return new self(
            type_chaudiere: $type_chaudiere,
            label: $label,
            priorite_cascade: $priorite_cascade,
            pn: $pn,
            scop: $scop,
            combustion: $combustion,
        );
    }
}
