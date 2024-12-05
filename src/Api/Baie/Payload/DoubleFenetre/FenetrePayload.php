<?php

namespace App\Api\Baie\Payload\DoubleFenetre;

use App\Api\Baie\Payload\{MenuiseriePayload, VitragePayload};
use App\Domain\Baie\Enum\TypeBaie;
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
        public ?float $ug,
        #[Assert\Positive]
        public ?float $uw,
        #[Assert\Positive]
        public ?float $sw,
    ) {}
}
