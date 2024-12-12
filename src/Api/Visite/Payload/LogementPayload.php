<?php

namespace App\Api\Visite\Payload;

use App\Domain\Visite\Enum\Typologie;
use Symfony\Component\Validator\Constraints as Assert;

final class LogementPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        public Typologie $typologie,
        #[Assert\Positive]
        public float $surface_habitable,
    ) {}
}
