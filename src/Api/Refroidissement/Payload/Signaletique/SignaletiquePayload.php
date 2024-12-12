<?php

namespace App\Api\Refroidissement\Payload\Signaletique;

use App\Domain\Refroidissement\ValueObject\Signaletique;

interface SignaletiquePayload
{
    public function to(): Signaletique;
}
