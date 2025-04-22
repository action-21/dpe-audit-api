<?php

namespace App\Api\Chauffage\Model;

use App\Domain\Common\ValueObject\{Consommation, Emission};
use App\Domain\Chauffage\Entity\Systeme as Entity;
use App\Domain\Chauffage\Enum\ConfigurationSysteme;
use App\Domain\Chauffage\ValueObject\{Intermittence, Rendement};

/**
 * @property array<Rendement> $rg
 * @property array<Rendement> $ich
 * @property array<Intermittence> $intermittences
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class SystemeData
{
    public function __construct(
        public readonly ?ConfigurationSysteme $configuration,
        public readonly ?float $rdim,
        public readonly ?float $rd,
        public readonly ?float $re,
        public readonly ?float $rr,
        /** @var Rendement[] */
        public readonly array $rg,
        /** @var Rendement[] */
        public readonly array $ich,
        /** @var Intermittence[] */
        public readonly array $intermittences,
        /** @var Consommation[] */
        public readonly array $consommations,
        /** @var Emission[] */
        public readonly array $emissions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            configuration: $entity->data()->configuration,
            rdim: $entity->data()->rdim,
            rd: $entity->data()->rd,
            re: $entity->data()->re,
            rr: $entity->data()->rr,
            rg: $entity->data()->rg?->values() ?? [],
            ich: $entity->data()->ich?->values() ?? [],
            intermittences: $entity->data()->intermittences?->values() ?? [],
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
