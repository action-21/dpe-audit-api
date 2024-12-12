<?php

namespace App\Api\Enveloppe\Payload;

use App\Domain\Enveloppe\Enum\Inertie;
use Symfony\Component\Validator\Constraints as Assert;

final class RefendPayload
{
    public function __construct(
        public string $description,
        #[Assert\Positive]
        public float $surface,
        public Inertie\InertieRefend $inertie,
    ) {}
}
