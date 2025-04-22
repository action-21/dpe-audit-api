<?php

namespace App\Api\Ventilation\Model;

use App\Domain\Common\ValueObject\{Consommation, Emission};
use App\Domain\Ventilation\Entity\Installation as Entity;

/**
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class InstallationData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?float $qvarep_conv,
        public readonly ?float $qvasouf_conv,
        public readonly ?float $smea_conv,
        /** @var array<Consommation> */
        public readonly array $consommations,
        /** @var array<Emission> */
        public readonly array $emissions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            rdim: $entity->data()->rdim,
            qvarep_conv: $entity->data()->qvarep_conv,
            qvasouf_conv: $entity->data()->qvasouf_conv,
            smea_conv: $entity->data()->smea_conv,
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
