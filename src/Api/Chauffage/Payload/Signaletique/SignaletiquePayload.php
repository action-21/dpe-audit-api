<?php

namespace App\Api\Chauffage\Payload\Signaletique;

use App\Domain\Chauffage\ValueObject\Signaletique;

interface SignaletiquePayload
{
    public function to(): Signaletique;
}
