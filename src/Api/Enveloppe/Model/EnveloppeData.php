<?php

namespace App\Api\Enveloppe\Model;

use App\Domain\Enveloppe\Enveloppe as Entity;
use App\Domain\Enveloppe\Enum\{ConfortEte, Inertie, Performance, TypeDeperdition};
use App\Domain\Enveloppe\ValueObject\{Deperdition, Permeabilite, SurfaceDeperditive};

/**
 * @property array<Deperdition> $deperditions
 * @property array<SurfaceDeperditive> $surfaces_deperditives
 * @property array<Apport> $apports
 */
final class EnveloppeData
{
    public function __construct(
        public ?Permeabilite $permeabilite,
        public ?float $dp,
        public ?float $dr,
        public ?float $pt,
        public ?float $gv,
        public ?float $ubat,
        public ?bool $inertie_lourde,
        public ?bool $planchers_hauts_isoles,
        public ?bool $presence_protections_solaires,
        public ?bool $logement_traversant,
        public ?bool $presence_brasseurs_air,
        public ?ConfortEte $confort_ete,
        public ?Performance $performance,
        public ?Inertie $inertie,
        /** @var Deperdition[] */
        public array $deperditions,
        /** @var SurfaceDeperditive[] */
        public array $surfaces_deperditives,
        /** @var Apport[] */
        public array $apports,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            permeabilite: $entity->data()->permeabilite,
            ubat: $entity->data()->ubat,
            gv: $entity->data()->deperditions?->get(),
            dp: $entity->data()->deperditions?->get(...TypeDeperdition::dp()),
            dr: $entity->data()->deperditions?->get(TypeDeperdition::RENOUVELEMENT_AIR),
            pt: $entity->data()->deperditions?->get(TypeDeperdition::PONT_THERMIQUE),
            inertie_lourde: $entity->data()->inertie_lourde,
            planchers_hauts_isoles: $entity->data()->planchers_hauts_isoles,
            presence_protections_solaires: $entity->data()->presence_protections_solaires,
            logement_traversant: $entity->data()->logement_traversant,
            presence_brasseurs_air: $entity->data()->presence_brasseurs_air,
            confort_ete: $entity->data()->confort_ete,
            performance: $entity->data()->performance,
            inertie: $entity->data()->inertie,
            deperditions: $entity->data()->deperditions?->values() ?? [],
            surfaces_deperditives: $entity->data()->surfaces_deperditives?->values() ?? [],
            apports: $entity->data()->apports ? Apport::from($entity->data()->apports) : [],
        );
    }
}
