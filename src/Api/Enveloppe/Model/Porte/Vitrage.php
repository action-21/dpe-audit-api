<?php

namespace App\Api\Enveloppe\Model\Porte;

use App\Domain\Enveloppe\Entity\Porte as Entity;
use App\Domain\Enveloppe\Enum\Porte\TypeVitrage;
use Symfony\Component\Validator\Constraints as Assert;

final class Vitrage
{
    public function __construct(
        #[Assert\PositiveOrZero]
        #[Assert\LessThanOrEqual(60)]
        public readonly ?float $taux_vitrage,

        public readonly ?TypeVitrage $type_vitrage,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            taux_vitrage: $entity->vitrage()->taux_vitrage?->value(),
            type_vitrage: $entity->vitrage()->type_vitrage,
        );
    }
}
