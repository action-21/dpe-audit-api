<?php

namespace App\Api\Resource\Simulation;

use App\Api\Resource\Audit\Audit;
use App\Api\Resource\Chauffage\Chauffage;
use App\Api\Resource\Eclairage\Eclairage;
use App\Api\Resource\Ecs\Ecs;
use App\Api\Resource\Enveloppe\Enveloppe;
use App\Api\Resource\Refroidissement\Refroidissement;
use App\Api\Resource\Ventilation\Ventilation;
use App\Api\Resource\Visite\Visite;
use App\Domain\Common\Type\Id;
use App\Domain\Simulation\Simulation as Entity;
use App\Domain\Simulation\ValueObject\{Bilan, Performance};
use ApiPlatform\Metadata\{ApiProperty, ApiResource, Get};

#[ApiResource(
    operations: [
        new Get(),
    ]
)]
final class Simulation
{
    public function __construct(
        #[ApiProperty(identifier: true, readable: false, writable: false)]
        public readonly Id $id,
        public readonly Audit $audit,
        public readonly Enveloppe $enveloppe,
        public readonly Chauffage $chauffage,
        public readonly Ecs $ecs,
        public readonly Ventilation $ventilation,
        public readonly Refroidissement $refroidissement,
        public readonly Eclairage $eclairage,
        public readonly Visite $visite,
        public readonly ?Bilan $bilan,
        /** @var Performance[] */
        public readonly array $performances,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            audit: Audit::from($entity->audit()),
            enveloppe: Enveloppe::from($entity->enveloppe()),
            chauffage: Chauffage::from($entity->chauffage()),
            ecs: Ecs::from($entity->ecs()),
            ventilation: Ventilation::from($entity->ventilation()),
            refroidissement: Refroidissement::from($entity->refroidissement()),
            eclairage: Eclairage::from($entity->eclairage()),
            visite: Visite::from($entity->visite()),
            performances: $entity->performances()?->values() ?? [],
            bilan: $entity->bilan(),
        );
    }
}
