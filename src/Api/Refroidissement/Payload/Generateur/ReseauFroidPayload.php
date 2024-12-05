<?php

namespace App\Api\Refroidissement\Payload\Generateur;

use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class ReseauFroidPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,

        #[AppAssert\ReseauFroid]
        public ?string $reseau_froid_id,
    ) {}
}
