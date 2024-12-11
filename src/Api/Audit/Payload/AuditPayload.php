<?php

namespace App\Api\Audit\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class AuditPayload
{
    public function __construct(
        #[Assert\Valid]
        public AdressePayload $adresse,
        #[Assert\Valid]
        public BatimentPayload $batiment,
        #[Assert\Valid]
        public ?LogementPayload $logement,
    ) {}
}
