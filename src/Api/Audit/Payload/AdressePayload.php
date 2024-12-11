<?php

namespace App\Api\Audit\Payload;

use App\Domain\Audit\ValueObject\Adresse;
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

    public function to(): Adresse
    {
        return Adresse::create(
            libelle: $this->libelle,
            code_postal: $this->code_postal,
            commune: $this->commune,
            ban_id: $this->ban_id,
            rnb_id: $this->rnb_id,
        );
    }
}
