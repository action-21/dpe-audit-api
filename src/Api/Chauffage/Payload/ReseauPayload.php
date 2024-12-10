<?php

namespace App\Api\Chauffage\Payload;

use App\Domain\Chauffage\Enum\{IsolationReseau, TypeDistribution};
use App\Domain\Chauffage\ValueObject\Reseau;
use Symfony\Component\Validator\Constraints as Assert;

final class ReseauPayload
{
    public function __construct(
        public TypeDistribution $type_distribution,
        public bool $presence_circulateur_externe,
        #[Assert\Positive]
        public int $niveaux_desservis,
        public IsolationReseau $isolation_reseau,
    ) {}

    public function to(): Reseau
    {
        return Reseau::create(
            type_distribution: $this->type_distribution,
            presence_circulateur_externe: $this->presence_circulateur_externe,
            niveaux_desservis: $this->niveaux_desservis,
            isolation_reseau: $this->isolation_reseau,
        );
    }
}
