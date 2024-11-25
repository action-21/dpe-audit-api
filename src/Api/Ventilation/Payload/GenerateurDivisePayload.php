<?php

namespace App\Api\Ventilation\Payload;

use App\Domain\Ventilation\Enum\TypeGenerateur;

final class GenerateurDivisePayload
{
    public function __construct(
        public string $id,
        public string $description,
        public ?int $annee_installation,
        public TypeGenerateur\TypeGenerateurDivise $type,
    ) {}
}
