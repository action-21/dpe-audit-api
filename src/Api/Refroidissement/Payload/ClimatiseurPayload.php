<?php

namespace App\Api\Refroidissement\Payload;

use App\Domain\Refroidissement\Enum\{EnergieGenerateur, TypeGenerateur};
use Symfony\Component\Validator\Constraints as Assert;

final class ClimatiseurPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public TypeGenerateur\Climatiseur $type,
        public EnergieGenerateur $energie,
        #[Assert\Positive]
        public ?float $seer,
    ) {}
}
