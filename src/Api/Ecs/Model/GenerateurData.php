<?php

namespace App\Api\Ecs\Model;

use App\Api\Common\Model\Perte;
use App\Domain\Common\ValueObject\{Consommation, Emission};
use App\Domain\Ecs\Entity\Generateur as Entity;

/**
 * @property array<Perte> $pertes
 * @property array<Perte> $pertes_recuperables
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class GenerateurData
{
    public function __construct(
        public readonly ?float $pecs,
        public readonly ?float $paux,
        public readonly ?float $pn,
        public readonly ?float $cop,
        public readonly ?float $rpn,
        public readonly ?float $qp0,
        public readonly ?float $pveilleuse,
        /** @var Perte[] */
        public readonly array $pertes,
        /** @var Perte[] */
        public readonly array $pertes_recuperables,
        /** @var Consommation[] */
        public readonly array $consommations,
        /** @var Emission[] */
        public readonly array $emissions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            pecs: $entity->data()->pecs,
            paux: $entity->data()->paux,
            pn: $entity->data()->pn,
            cop: $entity->data()->cop,
            rpn: $entity->data()->rpn?->value(),
            qp0: $entity->data()->qp0,
            pveilleuse: $entity->data()->pveilleuse,
            pertes: $entity->data()->pertes ? Perte::from($entity->data()->pertes) : [],
            pertes_recuperables: $entity->data()->pertes_recuperables ? Perte::from($entity->data()->pertes_recuperables) : [],
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
