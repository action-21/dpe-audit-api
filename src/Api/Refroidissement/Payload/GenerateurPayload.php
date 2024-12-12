<?php

namespace App\Api\Refroidissement\Payload;

use App\Api\Refroidissement\Payload\Signaletique\{ClimatiseurPayload, PacPayload, ReseauFroidPayload};
use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class GenerateurPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        #[Assert\Valid]
        public ClimatiseurPayload|PacPayload|ReseauFroidPayload $signaletique,
        public ?int $annee_installation,
        #[AppAssert\ReseauFroid]
        public ?string $reseau_froid_id,
    ) {}
}
