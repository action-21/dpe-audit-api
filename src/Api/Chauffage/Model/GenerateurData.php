<?php

namespace App\Api\Chauffage\Model;

use App\Api\Common\Model\Perte;
use App\Domain\Common\ValueObject\{Consommation, Emission};
use App\Domain\Chauffage\Entity\Generateur as Entity;

/**
 * @property array<Perte> $pertes
 * @property array<Perte> $pertes_recuperables
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class GenerateurData
{
    public function __construct(
        public readonly ?float $pch,
        public readonly ?float $pn,
        public readonly ?float $paux,
        public readonly ?float $scop,
        public readonly ?float $rpn,
        public readonly ?float $rpint,
        public readonly ?float $qp0,
        public readonly ?float $pveilleuse,
        public readonly ?float $tfonc30,
        public readonly ?float $tfonc100,
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
            pch: $entity->data()->pch,
            paux: $entity->data()->paux,
            pn: $entity->data()->pn,
            scop: $entity->data()->scop,
            rpn: $entity->data()->rpn?->value(),
            rpint: $entity->data()->rpint?->value(),
            qp0: $entity->data()->qp0,
            pveilleuse: $entity->data()->pveilleuse,
            tfonc30: $entity->data()->tfonc30,
            tfonc100: $entity->data()->tfonc100,
            pertes: $entity->data()->pertes ? Perte::from($entity->data()->pertes) : [],
            pertes_recuperables: $entity->data()->pertes_recuperables ? Perte::from($entity->data()->pertes_recuperables) : [],
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
