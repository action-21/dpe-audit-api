<?php

namespace App\Api\Chauffage\Payload;

use App\Domain\Chauffage\ValueObject\Solaire;
use Symfony\Component\Validator\Constraints as Assert;

final class SolairePayload
{
    public function __construct(
        #[Assert\PositiveOrZero]
        #[Assert\LessThanOrEqual(1)]
        public ?float $fch,
    ) {}

    public function to(): Solaire
    {
        return Solaire::create(
            fch: $this->fch,
        );
    }
}
