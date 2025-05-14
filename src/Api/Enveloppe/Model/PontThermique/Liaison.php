<?php

namespace App\Api\Enveloppe\Model\PontThermique;

use App\Domain\Enveloppe\Entity\PontThermique as Entity;
use App\Domain\Enveloppe\Enum\PontThermique\TypeLiaison;
use Symfony\Component\Validator\Constraints as Assert;

final class Liaison
{
    public function __construct(
        public TypeLiaison $type_liaison,

        public string $mur_id,

        public ?string $plancher_id,

        public ?string $ouverture_id,

        public ?bool $pont_thermique_partiel,
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
