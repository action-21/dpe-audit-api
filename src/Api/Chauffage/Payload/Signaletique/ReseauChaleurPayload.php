<?php

namespace App\Api\Chauffage\Payload\Signaletique;

use App\Domain\Chauffage\Enum\TypeGenerateur;
use App\Domain\Chauffage\ValueObject\Signaletique;

final class ReseauChaleurPayload implements SignaletiquePayload
{
    public function __construct(
        public TypeGenerateur\ReseauChaleur $type,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_reseau_chaleur();
    }
}
