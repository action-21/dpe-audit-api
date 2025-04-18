<?php

namespace App\Api\Enveloppe\Model\PontThermique;

use App\Domain\Enveloppe\Entity\PontThermique as Entity;
use App\Domain\Enveloppe\Enum\PontThermique\TypeLiaison;
use Symfony\Component\Validator\Constraints as Assert;

final class Liaison
{
    public function __construct(
        public readonly TypeLiaison $type_liaison,

        #[Assert\Uuid]
        public readonly string $mur_id,

        #[Assert\Uuid]
        public readonly ?string $plancher_id,

        #[Assert\Uuid]
        public readonly ?string $ouverture_id,

        public readonly ?bool $pont_thermique_partiel,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            type_liaison: $entity->liaison()->type,
            mur_id: $entity->liaison()->mur->id(),
            plancher_id: $entity->liaison()->plancher()?->id(),
            ouverture_id: $entity->liaison()->menuiserie()?->id(),
            pont_thermique_partiel: $entity->liaison()->pont_thermique_partiel,
        );
    }
}
