<?php

namespace App\Api\Baie\Payload\DoubleFenetre;

use App\Api\Baie\Payload\{MenuiseriePayload, VitragePayload};
use App\Domain\Baie\Enum\TypeBaie;
use App\Domain\Baie\ValueObject\DoubleFenetre;
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
        public ?float $ug,
        #[Assert\Positive]
        public ?float $uw,
        #[Assert\Positive]
        public ?float $sw,
    ) {}

    public function to(): DoubleFenetre
    {
        return DoubleFenetre::create_porte_fenetre(
            type: $this->type,
            presence_soubassement: $this->presence_soubassement,
            menuiserie: $this->menuiserie->to(),
            vitrage: $this->vitrage->to(),
            ug: $this->ug,
            uw: $this->uw,
            sw: $this->sw,
        );
    }
}
