<?php

namespace App\Api\Refroidissement\Model;

use App\Domain\Common\ValueObject\{Consommation, Emission};
use App\Domain\Refroidissement\Entity\Generateur as Entity;

/**
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class GenerateurData
{
    public function __construct(
        public ?float $eer,
        /** @var array<Consommation> */
        public array $consommations,
        /** @var array<Emission> */
        public array $emissions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            eer: $entity->data()->eer,
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
