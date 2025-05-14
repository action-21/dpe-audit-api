<?php

namespace App\Api\Ventilation\Model;

use App\Domain\Common\ValueObject\{Consommation, Emission};
use App\Domain\Ventilation\Ventilation as Entity;

/**
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class VentilationData
{
    public function __construct(
        public ?float $qvarep_conv,
        public ?float $qvasouf_conv,
        public ?float $smea_conv,
        /** @var array<Consommation> */
        public array $consommations,
        /** @var array<Emission> */
        public array $emissions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            qvarep_conv: $entity->data()->qvarep_conv,
            qvasouf_conv: $entity->data()->qvasouf_conv,
            smea_conv: $entity->data()->smea_conv,
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
