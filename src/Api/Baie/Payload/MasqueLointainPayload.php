<?php

namespace App\Api\Baie\Payload;

use App\Domain\Baie\Enum\TypeMasqueLointain;
use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class MasqueLointainPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        public TypeMasqueLointain $type,
        #[Assert\Positive]
        #[Assert\LessThan(90)]
        public float $hauteur,
        #[AppAssert\Orientation]
        public float $orientation,
    ) {}
}
