<?php

namespace App\Api\Chauffage\Resource;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\{Besoin, Consommation};
use App\Domain\Chauffage\Chauffage as Entity;

final class ChauffageResource
{
    public function __construct(
        public readonly Id $audit_id,
        /** @var GenerateurResource[] */
        public readonly array $generateurs,
        /** @var EmetteurResource[] */
        public readonly array $emetteurs,
        /** @var InstallationResource[] */
        public readonly array $installations,
        /** @var Besoin[] */
        public readonly array $besoins_bruts,
        /** @var Besoin[] */
        public readonly array $besoins,
        /** @var Consommation[] */
        public readonly array $consommations,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            audit_id: $entity->audit()->id(),
            generateurs: GenerateurResource::from_collection($entity->generateurs()),
            emetteurs: EmetteurResource::from_collection($entity->emetteurs()),
            installations: InstallationResource::from_collection($entity->installations()),
            besoins_bruts: $entity->besoins_bruts()?->values() ?? [],
            besoins: $entity->besoins()?->values() ?? [],
            consommations: $entity->installations()->consommations()?->values() ?? [],
        );
    }
}
