<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\Service\Assert;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Enum\UsageEcs;

final class Solaire
{
    public function __construct(
        public readonly UsageEcs $usage,
        public readonly ?int $annee_installation,
        public readonly ?float $fecs,
    ) {}

    public static function create(
        UsageEcs $usage,
        ?int $annee_installation,
        ?float $fecs,
    ): self {
        return new self(
            usage: $usage,
            annee_installation: $annee_installation,
            fecs: $fecs,
        );
    }

    public function controle(Ecs $entity): void
    {
        Assert::positif($this->fecs);
        Assert::inferieur_a($this->fecs, 1);
        Assert::annee($this->annee_installation);
        Assert::superieur_ou_egal_a($this->annee_installation, $entity->audit()->annee_construction_batiment());
    }
}
