<?php

namespace App\Api\Baie\Payload\Caracteristique;

use App\Api\Baie\Payload\{MenuiseriePayload, VitragePayload};
use App\Domain\Baie\Enum\{TypeBaie, TypeFermeture};
use App\Domain\Baie\ValueObject\Caracteristique;
use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class PorteFenetrePayload
{
    public function __construct(
        public TypeBaie\PorteFenetre $type,
        public bool $presence_soubassement,
        #[Assert\Valid]
        public MenuiseriePayload $menuiserie,
        #[Assert\Valid]
        public VitragePayload $vitrage,
        #[Assert\Positive]
        public float $surface,
        #[AppAssert\Inclinaison]
        public int $inclinaison,
        public TypeFermeture $type_fermeture,
        public bool $presence_protection_solaire,
        public ?int $annee_installation,
        #[Assert\Positive]
        public ?float $ug,
        #[Assert\Positive]
        public ?float $uw,
        #[Assert\Positive]
        public ?float $ujn,
        #[Assert\Positive]
        public ?float $sw,
    ) {}

    public function to(): Caracteristique
    {
        return Caracteristique::create_porte_fenetre(
            type: $this->type,
            presence_soubassement: $this->presence_soubassement,
            menuiserie: $this->menuiserie->to(),
            vitrage: $this->vitrage->to(),
            surface: $this->surface,
            inclinaison: $this->inclinaison,
            type_fermeture: $this->type_fermeture,
            presence_protection_solaire: $this->presence_protection_solaire,
            annee_installation: $this->annee_installation,
            ug: $this->ug,
            uw: $this->uw,
            ujn: $this->ujn,
            sw: $this->sw,
        );
    }
}
