<?php

namespace App\Application\Simulation\View;

use App\Application\Audit\View\Audit;
use App\Application\Chauffage\View\Chauffage;
use App\Application\Eclairage\View\Eclairage;
use App\Application\Ecs\View\Ecs;
use App\Application\Enveloppe\View\Enveloppe;
use App\Application\Refroidissement\View\Refroidissement;
use App\Application\Ventilation\View\Ventilation;
use App\Application\Visite\View\Visite;
use App\Domain\Simulation\Simulation as Entity;
use App\Domain\Simulation\ValueObject\{Bilan, Performance};

/**
 * @property Performance[] $performances
 */
final class Simulation
{
    public function __construct(
        public readonly Audit $audit,
        public readonly Enveloppe $enveloppe,
        public readonly Chauffage $chauffage,
        public readonly Ecs $ecs,
        public readonly Ventilation $ventilation,
        public readonly Refroidissement $refroidissement,
        public readonly Eclairage $eclairage,
        public readonly Visite $visite,
        public readonly ?Bilan $bilan,
        public readonly array $performances,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
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
