<?php

namespace App\Api\Ventilation\Payload;

use App\Domain\Ventilation\Enum\TypeGenerateur;

final class GenerateurCentralPayload
{
    public function __construct(
        public string $id,
        public string $description,
        public ?int $annee_installation,
        public TypeGenerateur\TypeGenerateurCentralise $type,
        public bool $presence_echangeur_thermique,
        public bool $generateur_collectif,
    ) {}
}
