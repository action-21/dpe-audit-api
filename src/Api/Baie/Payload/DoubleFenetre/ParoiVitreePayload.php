<?php

namespace App\Api\Baie\Payload\DoubleFenetre;

use App\Domain\Baie\Enum\TypeBaie;
use App\Domain\Baie\ValueObject\DoubleFenetre;
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

    public function to(): DoubleFenetre
    {
        return DoubleFenetre::create_paroi_vitree(
            type: $this->type,
            ug: $this->ug,
            uw: $this->uw,
            sw: $this->sw,
        );
    }
}
