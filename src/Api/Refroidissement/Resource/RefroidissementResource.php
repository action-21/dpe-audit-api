<?php

namespace App\Api\Refroidissement\Resource;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Common\ValueObject\{Besoin, Consommation};
use App\Domain\Refroidissement\Refroidissement as Entity;

final class RefroidissementResource
{
    public function __construct(
        public readonly Id $audit_id,
        /** @var GenerateurResource[] */
        public readonly array $generateurs,
        /** @var InstallationResource[] */
        public readonly array $installations,
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
            installations: InstallationResource::from_collection($entity->installations()),
            besoins: $entity->besoins()?->values() ?? [],
            consommations: $entity->installations()->consommations()?->values() ?? [],
        );
    }
}
