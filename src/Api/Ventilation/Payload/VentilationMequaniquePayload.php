<?php

namespace App\Api\Ventilation\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class VentilationMequaniquePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        #[Assert\Uuid]
        public string $generateur_id,
    ) {}
}
