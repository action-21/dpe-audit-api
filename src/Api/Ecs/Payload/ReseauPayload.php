<?php

namespace App\Api\Ecs\Payload;

use App\Domain\Ecs\Enum\{BouclageReseau, IsolationReseau};
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
}
