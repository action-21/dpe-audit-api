<?php

namespace App\Api\Baie\Payload;

use App\Domain\Baie\Enum\{NatureGazLame, TypeVitrage};
use App\Domain\Baie\ValueObject\Vitrage;
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

    public function to(): Vitrage
    {
        return Vitrage::create(
            type_vitrage: $this->type_vitrage,
            nature_gaz_lame: $this->nature_gaz_lame,
            epaisseur_lame: $this->epaisseur_lame,
            survitrage: $this->survitrage?->to(),
        );
    }
}
