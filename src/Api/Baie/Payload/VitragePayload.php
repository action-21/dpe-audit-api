<?php

namespace App\Api\Baie\Payload;

use App\Domain\Baie\Enum\{NatureGazLame, TypeVitrage};
use Symfony\Component\Validator\Constraints as Assert;

final class VitragePayload
{
    public function __construct(
        public TypeVitrage $type_vitrage,
        public ?NatureGazLame $nature_gaz_lame,
        #[Assert\Positive]
        public ?int $epaisseur_lame,
        #[Assert\Valid]
        public ?SurvitragePayload $survitrage,
    ) {}
}
