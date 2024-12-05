<?php

namespace App\Api\Baie\Payload\DoubleFenetre;

use App\Domain\Baie\Enum\TypeBaie;
use Symfony\Component\Validator\Constraints as Assert;

final class ParoiVitreePayload
{
    public function __construct(
        public TypeBaie\ParoiVitree $type,
        #[Assert\Positive]
        public ?float $ug,
        #[Assert\Positive]
        public ?float $uw,
        #[Assert\Positive]
        public ?float $sw,
    ) {}
}
