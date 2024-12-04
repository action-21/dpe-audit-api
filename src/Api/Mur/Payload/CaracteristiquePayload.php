<?php

namespace App\Api\Mur\Payload;

use App\Domain\Mur\Enum\{Inertie, TypeDoublage, TypeMur};
use App\Domain\Mur\Mur;
use App\Domain\Mur\ValueObject\Caracteristique;
use Symfony\Component\Validator\Constraints as Assert;

final class CaracteristiquePayload
{
    public function __construct(
        public TypeMur $type,
        public TypeDoublage $type_doublage,
        public Inertie $inertie,
        #[Assert\Positive]
        public float $surface,
        #[Assert\Positive]
        public ?int $epaisseur,
        public bool $paroi_ancienne,
        public bool $presence_enduit_isolant,
        public ?int $annee_construction,
        public ?int $annee_renovation,
        #[Assert\Positive]
        public ?float $u0,
        #[Assert\Positive]
        public ?float $u,
    ) {}

    public static function from(Mur $entity): self
    {
        return new self(
            type: $entity->caracteristique()->type,
            type_doublage: $entity->caracteristique()->type_doublage,
            inertie: $entity->caracteristique()->inertie,
            surface: $entity->caracteristique()->surface,
            epaisseur: $entity->caracteristique()->epaisseur,
            paroi_ancienne: $entity->caracteristique()->paroi_ancienne,
            presence_enduit_isolant: $entity->caracteristique()->presence_enduit_isolant,
            annee_construction: $entity->caracteristique()->annee_construction,
            annee_renovation: $entity->caracteristique()->annee_renovation,
            u0: $entity->caracteristique()->u0,
            u: $entity->caracteristique()->u,
        );
    }

    public function to(): Caracteristique
    {
        return Caracteristique::create(
            type: $this->type,
            type_doublage: $this->type_doublage,
            inertie: $this->inertie,
            surface: $this->surface,
            epaisseur: $this->epaisseur,
            paroi_ancienne: $this->paroi_ancienne,
            presence_enduit_isolant: $this->presence_enduit_isolant,
            annee_construction: $this->annee_construction,
            annee_renovation: $this->annee_renovation,
            u0: $this->u0,
            u: $this->u,
        );
    }
}
