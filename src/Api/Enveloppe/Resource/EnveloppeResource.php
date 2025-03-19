<?php

namespace App\Api\Enveloppe\Resource;

use App\Api\Baie\Resource\BaieResource;
use App\Api\Lnc\Resource\LncResource;
use App\Api\Mur\Resource\MurResource;
use App\Api\PlancherBas\Resource\PlancherBasResource;
use App\Api\PlancherHaut\Resource\PlancherHautResource;
use App\Api\PontThermique\Resource\PontThermiqueResource;
use App\Api\Porte\Resource\PorteResource;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe as Entity;
use App\Domain\Enveloppe\Enum\Exposition;
use App\Domain\Enveloppe\ValueObject\{Apport, Inertie, Performance, Permeabilite};

final class EnveloppeResource
{
    public function __construct(
        public readonly Id $audit_id,
        public readonly Exposition $exposition,
        public readonly ?float $q4pa_conv,
        public readonly ?Inertie $inertie,
        public readonly ?Permeabilite $permeabilite,
        public readonly ?Performance $performance,
        /** @var LncResource[] */
        public readonly array $locaux_non_chauffes,
        /** @var MurResource[] */
        public readonly array $murs,
        /** @var PlancherBasResource[] */
        public readonly array $planchers_bas,
        /** @var PlancherHautResource[] */
        public readonly array $planchers_hauts,
        /** @var BaieResource[] */
        public readonly array $baies,
        /** @var PorteResource[] */
        public readonly array $portes,
        /** @var PontThermiqueResource[] */
        public readonly array $ponts_thermiques,
        /** @var RefendResource[] */
        public readonly array $refends,
        /** @var PlancherIntermediaireResource[] */
        public readonly array $planchers_intermediaires,
        /** @var Apport[] */
        public readonly array $apports,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            audit_id: $entity->audit()->id(),
            exposition: $entity->exposition(),
            q4pa_conv: $entity->q4pa_conv(),
            locaux_non_chauffes: LncResource::from_collection($entity->locaux_non_chauffes()),
            murs: MurResource::from_collection($entity->parois()->murs()),
            planchers_bas: PlancherBasResource::from_collection($entity->parois()->planchers_bas()),
            planchers_hauts: PlancherHautResource::from_collection($entity->parois()->planchers_hauts()),
            baies: BaieResource::from_collection($entity->parois()->baies()),
            portes: PorteResource::from_collection($entity->parois()->portes()),
            ponts_thermiques: PontThermiqueResource::from_collection($entity->ponts_thermiques()),
            refends: RefendResource::from_collection($entity->refends()),
            planchers_intermediaires: PlancherIntermediaireResource::from_collection($entity->planchers_intermediaires()),
            inertie: $entity->inertie(),
            permeabilite: $entity->permeabilite(),
            performance: $entity->performance(),
            apports: $entity->apports()?->values() ?? [],
        );
    }
}
