<?php

namespace App\Api\Ecs\Model;

use App\Domain\Ecs\Entity\Installation as Entity;
use App\Domain\Ecs\Enum\UsageEcs;
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Solaire
{
    public function __construct(
        public readonly UsageEcs $usage,

        #[DpeAssert\Annee]
        public readonly ?int $annee_installation,

        #[Assert\PositiveOrZero]
        #[Assert\LessThanOrEqual(100)]
        public readonly ?float $fecs,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            usage: $entity->solaire_thermique()?->usage,
            fecs: $entity->solaire_thermique()?->fecs?->value(),
            annee_installation: $entity->solaire_thermique()?->annee_installation?->value(),
        );
    }
}
