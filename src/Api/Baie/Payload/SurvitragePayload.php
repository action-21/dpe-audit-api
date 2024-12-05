<?php

namespace App\Api\Baie\Payload;

use App\Domain\Baie\Enum\TypeSurvitrage;
use Symfony\Component\Validator\Constraints as Assert;

final class SurvitragePayload
{
    public function __construct(
        public TypeSurvitrage $type_survitrage,
        #[Assert\Positive]
        public ?int $epaisseur_lame,
    ) {}
}
