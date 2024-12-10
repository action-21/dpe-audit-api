<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Chauffage\Enum\{IsolationReseau, TypeDistribution};
use Webmozart\Assert\Assert;

final class Reseau
{
    public function __construct(
        public readonly TypeDistribution $type_distribution,
        public readonly bool $presence_circulateur_externe,
        public readonly int $niveaux_desservis,
        public readonly IsolationReseau $isolation_reseau,
    ) {}

    public static function create(
        TypeDistribution $type_distribution,
        bool $presence_circulateur_externe,
        int $niveaux_desservis,
        IsolationReseau $isolation_reseau,
    ): self {
        Assert::greaterThan($niveaux_desservis, 0);

        return new self(
            type_distribution: $type_distribution,
            presence_circulateur_externe: $presence_circulateur_externe,
            niveaux_desservis: $niveaux_desservis,
            isolation_reseau: $isolation_reseau,
        );
    }
}
