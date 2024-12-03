<?php

namespace App\Api\PlancherHaut\Payload;

use App\Domain\PlancherHaut\Enum\{Inertie, TypePlancherHaut};
use App\Domain\PlancherHaut\ValueObject\Caracteristique;
use Symfony\Component\Validator\Constraints as Assert;

final class CaracteristiquePayload
{
    public function __construct(
        public TypePlancherHaut $type,
        public Inertie $inertie,
        #[Assert\Positive]
        public float $surface,
        public ?int $annee_construction,
        public ?int $annee_renovation,
        #[Assert\Positive]
        public ?float $u0,
        #[Assert\Positive]
        public ?float $u,
    ) {}

    public function to(): Caracteristique
    {
        return Caracteristique::create(
            type: $this->type,
            inertie: $this->inertie,
            surface: $this->surface,
            annee_construction: $this->annee_construction,
            annee_renovation: $this->annee_renovation,
            u0: $this->u0,
            u: $this->u,
        );
    }
}
