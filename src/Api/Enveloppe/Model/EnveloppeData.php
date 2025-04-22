<?php

namespace App\Api\Enveloppe\Model;

use App\Domain\Enveloppe\Enveloppe as Entity;
use App\Domain\Enveloppe\Enum\{Inertie, Performance};
use App\Domain\Enveloppe\ValueObject\{Deperdition, Permeabilite, SurfaceDeperditive};

/**
 * @property array<Deperdition> $deperditions
 * @property array<SurfaceDeperditive> $surfaces_deperditives
 * @property array<Apport> $apports
 */
final class EnveloppeData
{
    public function __construct(
        public readonly ?Permeabilite $permeabilite,
        public readonly ?float $gv,
        public readonly ?float $ubat,
        public readonly ?Performance $performance,
        public readonly ?Inertie $inertie,
        /** @var Deperdition[] */
        public readonly array $deperditions,
        /** @var SurfaceDeperditive[] */
        public readonly array $surfaces_deperditives,
        /** @var Apport[] */
        public readonly array $apports,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            permeabilite: $entity->data()->permeabilite,
            ubat: $entity->data()->ubat,
            gv: $entity->data()->deperditions?->get(),
            performance: $entity->data()->performance,
            inertie: $entity->data()->inertie,
            deperditions: $entity->data()->deperditions?->values() ?? [],
            surfaces_deperditives: $entity->data()->surfaces_deperditives?->values() ?? [],
            apports: $entity->data()->apports ? Apport::from($entity->data()->apports) : [],
        );
    }
}
