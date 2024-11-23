<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Chauffage\Enum\IsolationReseau;
use App\Domain\Common\Service\Assert;

final class Reseau
{
    public function __construct(
        public readonly bool $presence_circulateur_externe,
        public readonly int $niveaux_desservis,
        public readonly IsolationReseau $isolation_reseau,
    ) {}

    public static function create(
        bool $presence_circulateur_externe,
        int $niveaux_desservis,
        IsolationReseau $isolation_reseau,
    ): self {
        return new self(
            presence_circulateur_externe: $presence_circulateur_externe,
            niveaux_desservis: $niveaux_desservis,
            isolation_reseau: $isolation_reseau,
        );
    }

    public function controle(): void
    {
        Assert::positif($this->niveaux_desservis);
    }
}
