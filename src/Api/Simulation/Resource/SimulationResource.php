<?php

namespace App\Api\Simulation\Resource;

use App\Api\Audit\Resource\AuditResource;
use App\Api\Enveloppe\Resource\EnveloppeResource;
use App\Api\Chauffage\Resource\ChauffageResource;
use App\Api\Ecs\Resource\EcsResource;
use App\Api\Ventilation\Resource\VentilationResource;
use App\Api\Refroidissement\Resource\RefroidissementResource;
use App\Api\Eclairage\Resource\EclairageResource;
use App\Api\Visite\Resource\VisiteResource;
use App\Domain\Common\Type\Id;
use App\Domain\Simulation\Simulation as Entity;
use App\Domain\Simulation\ValueObject\{Bilan, Performance};

final class SimulationResource
{
    public function __construct(
        public readonly Id $id,
        public readonly AuditResource $audit,
        public readonly EnveloppeResource $enveloppe,
        public readonly ChauffageResource $chauffage,
        public readonly EcsResource $ecs,
        public readonly VentilationResource $ventilation,
        public readonly RefroidissementResource $refroidissement,
        public readonly EclairageResource $eclairage,
        public readonly VisiteResource $visite,
        public readonly ?Bilan $bilan,
        /** @var Performance[] */
        public readonly array $performances,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            audit: AuditResource::from($entity->audit()),
            enveloppe: EnveloppeResource::from($entity->enveloppe()),
            chauffage: ChauffageResource::from($entity->chauffage()),
            ecs: EcsResource::from($entity->ecs()),
            ventilation: VentilationResource::from($entity->ventilation()),
            refroidissement: RefroidissementResource::from($entity->refroidissement()),
            eclairage: EclairageResource::from($entity->eclairage()),
            visite: VisiteResource::from($entity->visite()),
            performances: $entity->performances()?->values() ?? [],
            bilan: $entity->bilan(),
        );
    }
}
