<?php

namespace App\Api\Baie\Payload\Caracteristique;

use App\Api\Baie\Payload\{MenuiseriePayload, VitragePayload};
use App\Domain\Baie\Enum\{TypeBaie, TypeFermeture};
use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class FenetrePayload
{
    public function __construct(
        public TypeBaie\Fenetre $type,
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
}
