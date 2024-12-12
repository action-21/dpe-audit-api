<?php

namespace App\Api\Porte\Payload;

use App\Domain\Porte\Enum\TypeVitrage;
use App\Domain\Porte\ValueObject\Vitrage;
use Symfony\Component\Validator\Constraints as Assert;

final class VitragePayload
{
    public function __construct(
        #[Assert\PositiveOrZero]
        #[Assert\LessThanOrEqual(60)]
        public int $taux_vitrage,
        public ?TypeVitrage $type_vitrage,
    ) {}

    public function to(): Vitrage
    {
        return Vitrage::create(
            taux_vitrage: $this->taux_vitrage,
            type_vitrage: $this->type_vitrage,
        );
    }
}
