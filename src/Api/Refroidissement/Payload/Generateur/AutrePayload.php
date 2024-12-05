<?php

namespace App\Api\Refroidissement\Payload\Generateur;

use App\Domain\Refroidissement\Enum\EnergieGenerateur;
use Symfony\Component\Validator\Constraints as Assert;

final class AutrePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,

        public EnergieGenerateur $energie,

        #[Assert\Positive]
        public ?float $seer,
    ) {}
}
