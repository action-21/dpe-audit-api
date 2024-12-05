<?php

namespace App\Api\Audit\Payload;

use App\Services\Validator\Constraints as AppAssert;

final class AdressePayload
{
    public function __construct(
        public string $libelle,
        #[AppAssert\CodePostal]
        public string $code_postal,
        public string $commune,
        public ?string $ban_id,
        public ?string $rnb_id,
    ) {}
}
