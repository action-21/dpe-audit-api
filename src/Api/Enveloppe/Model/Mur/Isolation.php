<?php

namespace App\Api\Enveloppe\Model\Mur;

use App\Domain\Enveloppe\Enum\{EtatIsolation, TypeIsolation};
use App\Domain\Enveloppe\Entity\Mur as Entity;
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Isolation
{
    public function __construct(
        public readonly ?EtatIsolation $etat_isolation,

        public readonly ?TypeIsolation $type_isolation,

        #[Assert\Positive]
        public readonly ?float $epaisseur_isolation,

        #[DpeAssert\Annee]
        public readonly ?int $annee_isolation,

        #[Assert\Positive]
        public readonly ?float $resistance_thermique_isolation,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            etat_isolation: $entity->isolation()->etat_isolation,
            type_isolation: $entity->isolation()->type_isolation,
            epaisseur_isolation: $entity->isolation()->epaisseur_isolation,
            annee_isolation: $entity->isolation()->annee_isolation?->value,
            resistance_thermique_isolation: $entity->isolation()->resistance_thermique_isolation,
        );
    }
}
