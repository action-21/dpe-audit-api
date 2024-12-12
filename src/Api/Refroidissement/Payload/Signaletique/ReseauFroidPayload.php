<?php

namespace App\Api\Refroidissement\Payload\Signaletique;

use App\Domain\Refroidissement\Enum\TypeGenerateur;
use App\Domain\Refroidissement\ValueObject\Signaletique;

final class ReseauFroidPayload implements SignaletiquePayload
{
    public function __construct(
        public TypeGenerateur\ReseauFroid $type_generateur,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_reseau_froid();
    }
}
