<?php

namespace App\Application\Enveloppe\View;

use App\Application\Baie\View\Baie;
use App\Application\Lnc\View\Lnc;
use App\Application\Mur\View\Mur;
use App\Application\PlancherBas\View\PlancherBas;
use App\Application\PlancherHaut\View\PlancherHaut;
use App\Application\PontThermique\View\PontThermique;
use App\Application\Porte\View\Porte;
use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enveloppe as Entity;
use App\Domain\Enveloppe\Enum\Exposition;
use App\Domain\Enveloppe\ValueObject\{Apport, Inertie, Performance, Permeabilite};

/**
 * @property Lnc[] $locaux_non_chauffes
 * @property Mur[] $murs
 * @property PlancherBas[] $planchers_bas
 * @property PlancherHaut[] $planchers_hauts
 * @property Baie[] $baies
 * @property Porte[] $portes
 * @property PontThermique[] $ponts_thermiques
 * @property Refend[] $refends
 * @property PlancherIntermediaire[] $planchers_intermediaires
 * @property Apport[] $apports
 */
final class Enveloppe
{
    public function __construct(
        public readonly Id $audit_id,
        public readonly Exposition $exposition,
        public readonly ?float $q4pa_conv,
        public readonly ?Inertie $inertie,
        public readonly ?Permeabilite $permeabilite,
        public readonly ?Performance $performance,
        public readonly array $locaux_non_chauffes,
        public readonly array $murs,
        public readonly array $planchers_bas,
        public readonly array $planchers_hauts,
        public readonly array $baies,
        public readonly array $portes,
        public readonly array $ponts_thermiques,
        public readonly array $refends,
        public readonly array $planchers_intermediaires,
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
