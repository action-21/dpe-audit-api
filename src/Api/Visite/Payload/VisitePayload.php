<?php

namespace App\Api\Visite\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class VisitePayload
{
    public function __construct(
        #[Assert\All([new Assert\Type(LogementPayload::class)])]
        #[Assert\Valid]
        public array $logements,
    ) {}
}
