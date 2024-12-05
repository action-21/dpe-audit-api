<?php

namespace App\Api\Ventilation\Payload\Systeme;

use Symfony\Component\Validator\Constraints as Assert;

final class VentilationDiviseePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        #[Assert\Uuid]
        public string $generateur_id,
    ) {}
}
