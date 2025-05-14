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
        public ?float $rdim,
        public ?float $qvarep_conv,
        public ?float $qvasouf_conv,
        public ?float $smea_conv,
        public ?float $ratio_utilisation,
        public ?float $pvent_moy,
        public ?float $pvent,
        /** @var array<Consommation> */
        public array $consommations,
        /** @var array<Emission> */
        public array $emissions,
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
