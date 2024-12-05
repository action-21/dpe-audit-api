<?php

namespace App\Api\Refroidissement\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class SystemePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,

        #[Assert\Uuid]
        public string $generateur_id,
    ) {}
}
