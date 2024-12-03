<?php

namespace App\Api\Porte\Payload;

use App\Domain\Porte\Enum\EtatIsolation;
use App\Domain\Porte\ValueObject\Caracteristique;
use Symfony\Component\Validator\Constraints as Assert;

final class CaracteristiquePayload
{
    public function __construct(
        #[Assert\Positive]
        public float $surface,
        public bool $presence_sas,
        public EtatIsolation $isolation,
        #[Assert\Valid]
        public MenuiseriePayload $menuiserie,
        #[Assert\Valid]
        public ?VitragePayload $vitrage,
        public ?int $annee_installation,
        #[Assert\Positive]
        public ?float $u,
    ) {}

    public function to(): Caracteristique
    {
        return Caracteristique::create(
            surface: $this->surface,
            presence_sas: $this->presence_sas,
            isolation: $this->isolation,
            menuiserie: $this->menuiserie->to(),
            vitrage: $this->vitrage?->to(),
            annee_installation: $this->annee_installation,
            u: $this->u,
        );
    }
}
