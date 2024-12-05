<?php

namespace App\Api\Ventilation\Payload\Generateur;

use App\Domain\Ventilation\Enum\TypeGenerateur;
use Symfony\Component\Validator\Constraints as Assert;

final class GenerateurDivisePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        public ?int $annee_installation,
        public TypeGenerateur\TypeGenerateurDivise $type,
    ) {}
}
