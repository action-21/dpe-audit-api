<?php

namespace App\Api\Ecs\Payload\Signaletique;

use App\Domain\Ecs\ValueObject\Signaletique;

interface SignaletiquePayload
{
    public function to(): Signaletique;
}
