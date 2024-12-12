<?php

namespace App\Api\Ecs\Payload;

use App\Domain\Ecs\Enum\{BouclageReseau, IsolationReseau};
use App\Domain\Ecs\ValueObject\Reseau;
use Symfony\Component\Validator\Constraints as Assert;

final class ReseauPayload
{
    public function __construct(
        public bool $alimentation_contigues,
        #[Assert\Positive]
        public int $niveaux_desservis,
        public IsolationReseau $isolation_reseau,
        public BouclageReseau $type_bouclage,
    ) {}

    public function to(): Reseau
    {
        return Reseau::create(
            alimentation_contigues: $this->alimentation_contigues,
            niveaux_desservis: $this->niveaux_desservis,
            isolation_reseau: $this->isolation_reseau,
            type_bouclage: $this->type_bouclage,
        );
    }
}
