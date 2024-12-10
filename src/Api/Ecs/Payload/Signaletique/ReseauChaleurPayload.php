<?php

namespace App\Api\Ecs\Payload\Signaletique;

use App\Domain\Ecs\Enum\TypeGenerateur;
use App\Domain\Ecs\ValueObject\Signaletique;

final class ReseauChaleurPayload
{
    public function __construct(
        public TypeGenerateur\ReseauChaleur $type,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_reseau_chaleur();
    }
}
