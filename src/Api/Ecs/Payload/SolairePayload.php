<?php

namespace App\Api\Ecs\Payload;

use App\Domain\Ecs\Enum\UsageEcs;
use App\Domain\Ecs\ValueObject\Solaire;
use Symfony\Component\Validator\Constraints as Assert;

final class SolairePayload
{
    public function __construct(
        public UsageEcs $usage,
        public ?int $annee_installation,
        #[Assert\PositiveOrZero]
        #[Assert\LessThanOrEqual(1)]
        public ?float $fecs,
    ) {}

    public function to(): Solaire
    {
        return Solaire::create(
            usage: $this->usage,
            annee_installation: $this->annee_installation,
            fecs: $this->fecs,
        );
    }
}
