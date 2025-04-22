<?php

namespace App\Api\Ventilation\Model;

use App\Domain\Common\ValueObject\{Consommation, Emission};
use App\Domain\Ventilation\Entity\Systeme as Entity;

/**
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class SystemeData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?float $qvarep_conv,
        public readonly ?float $qvasouf_conv,
        public readonly ?float $smea_conv,
        public readonly ?float $ratio_utilisation,
        public readonly ?float $pvent_moy,
        public readonly ?float $pvent,
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
            ratio_utilisation: $entity->data()->ratio_utilisation,
            pvent_moy: $entity->data()->pvent_moy,
            pvent: $entity->data()->pvent,
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
