<?php

namespace App\Api\Audit;

use App\Api\Audit\Payload\AuditPayload;
use App\Domain\Audit\{Audit, AuditFactory};

final class CreateAuditHandler
{
    public function __construct(private AuditFactory $factory) {}

    public function __invoke(AuditPayload $payload): Audit
    {
        return $this->factory->build(
            adresse: $payload->adresse->to(),
            batiment: $payload->batiment->to(),
            logement: $payload->logement?->to(),
        );
    }
}
