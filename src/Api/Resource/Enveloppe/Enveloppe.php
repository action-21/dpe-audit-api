<?php

namespace App\Api\Resource\Enveloppe;

use App\Api\Resource\Baie\Baie;
use App\Api\Resource\Lnc\Lnc;
use App\Api\Resource\Mur\Mur;
use App\Api\Resource\PlancherBas\PlancherBas;
use App\Api\Resource\PlancherHaut\PlancherHaut;
use App\Api\Resource\PontThermique\PontThermique;
use App\Api\Resource\Porte\Porte;
use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enveloppe as Entity;
use App\Domain\Enveloppe\Enum\Exposition;
use App\Domain\Enveloppe\ValueObject\{Apport, Inertie, Performance, Permeabilite};
use ApiPlatform\Metadata\{ApiProperty, ApiResource};

final class Enveloppe
{
    public function __construct(
        #[ApiProperty(identifier: true, readable: false, writable: false)]
        public readonly Id $audit_id,
        public readonly Exposition $exposition,
        public readonly ?float $q4pa_conv,
        public readonly ?Inertie $inertie,
        public readonly ?Permeabilite $permeabilite,
        public readonly ?Performance $performance,
        /** @var Lnc[] */
        public readonly array $locaux_non_chauffes,
        /** @var Mur[] */
        public readonly array $murs,
        /** @var PlancherBas[] */
        public readonly array $planchers_bas,
        /** @var PlancherHaut[] */
        public readonly array $planchers_hauts,
        /** @var Baie[] */
        public readonly array $baies,
        /** @var Porte[] */
        public readonly array $portes,
        /** @var PontThermique[] */
        public readonly array $ponts_thermiques,
        /** @var Refend[] */
        public readonly array $refends,
        /** @var PlancherIntermediaire[] */
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
            locaux_non_chauffes: Lnc::from_collection($entity->locaux_non_chauffes()),
            murs: Mur::from_collection($entity->parois()->murs()),
            planchers_bas: PlancherBas::from_collection($entity->parois()->planchers_bas()),
            planchers_hauts: PlancherHaut::from_collection($entity->parois()->planchers_hauts()),
            baies: Baie::from_collection($entity->parois()->baies()),
            portes: Porte::from_collection($entity->parois()->portes()),
            ponts_thermiques: PontThermique::from_collection($entity->ponts_thermiques()),
            refends: Refend::from_collection($entity->refends()),
            planchers_intermediaires: PlancherIntermediaire::from_collection($entity->planchers_intermediaires()),
            inertie: $entity->inertie(),
            permeabilite: $entity->permeabilite(),
            performance: $entity->performance(),
            apports: $entity->apports()?->values() ?? [],
        );
    }
}
