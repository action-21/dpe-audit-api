<?php

namespace App\Api\Ventilation\Payload\Generateur;

use App\Domain\Ventilation\Enum\TypeGenerateur;
use Symfony\Component\Validator\Constraints as Assert;

final class GenerateurCentralPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        public ?int $annee_installation,
        public TypeGenerateur\TypeGenerateurCentralise $type,
        public bool $presence_echangeur_thermique,
        public bool $generateur_collectif,
    ) {}
}
