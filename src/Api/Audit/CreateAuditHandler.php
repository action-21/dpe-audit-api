<?php

namespace App\Api\Audit;

use App\Api\Audit\Payload\AuditPayload;
use App\Domain\Audit\Audit;

final class CreateAuditHandler
{
    public function __invoke(AuditPayload $payload): Audit
    {
        return Audit::create(
            adresse: $payload->adresse->to(),
            batiment: $payload->batiment->to(),
            logement: $payload->logement?->to(),
        );
    }
}
