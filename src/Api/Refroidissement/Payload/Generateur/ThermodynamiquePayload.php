<?php

namespace App\Api\Refroidissement\Payload\Generateur;

use App\Domain\Refroidissement\Enum\TypeGenerateur;
use Symfony\Component\Validator\Constraints as Assert;

final class ThermodynamiquePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,

        public TypeGenerateur\TypeThermodynamique $type,

        #[Assert\Positive]
        public ?float $seer,
    ) {}
}
