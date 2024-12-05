<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Ecs\Enum\{BouclageReseau, IsolationReseau};
use Webmozart\Assert\Assert;

final class Reseau
{
    public function __construct(
        public readonly bool $alimentation_contigues,
        public readonly int $niveaux_desservis,
        public readonly IsolationReseau $isolation_reseau,
        public readonly BouclageReseau $type_bouclage,
    ) {}

    public static function create(
        bool $alimentation_contigues,
        int $niveaux_desservis,
        IsolationReseau $isolation_reseau,
        BouclageReseau $type_bouclage,
    ): self {
        Assert::greaterThan($niveaux_desservis, 0);

        return new self(
            alimentation_contigues: $alimentation_contigues,
            niveaux_desservis: $niveaux_desservis,
            isolation_reseau: $isolation_reseau,
            type_bouclage: $type_bouclage,
        );
    }
}
