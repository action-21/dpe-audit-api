<?php

namespace App\Api\Baie\Payload;

use App\Domain\Baie\Enum\TypeSurvitrage;
use App\Domain\Baie\ValueObject\Survitrage;
use Symfony\Component\Validator\Constraints as Assert;

final class SurvitragePayload
{
    public function __construct(
        public TypeSurvitrage $type_survitrage,
        #[Assert\Positive]
        public ?int $epaisseur_lame,
    ) {}

    public function to(): Survitrage
    {
        return Survitrage::create(
            type_survitrage: $this->type_survitrage,
            epaisseur_lame: $this->epaisseur_lame,
        );
    }
}
