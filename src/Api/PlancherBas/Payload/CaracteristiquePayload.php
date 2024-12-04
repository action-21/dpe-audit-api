<?php

namespace App\Api\PlancherBas\Payload;

use App\Domain\PlancherBas\Enum\{Inertie, TypePlancherBas};
use App\Domain\PlancherBas\ValueObject\Caracteristique;
use Symfony\Component\Validator\Constraints as Assert;

final class CaracteristiquePayload
{
    public function __construct(
        public TypePlancherBas $type,
        public Inertie $inertie,
        #[Assert\Positive]
        public float $surface,
        #[Assert\Positive]
        public float $perimetre,
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
            perimetre: $this->perimetre,
            annee_construction: $this->annee_construction,
            annee_renovation: $this->annee_renovation,
            u0: $this->u0,
            u: $this->u,
        );
    }
}
