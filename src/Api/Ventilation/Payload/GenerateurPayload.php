<?php

namespace App\Api\Ventilation\Payload;

use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVmc};
use Symfony\Component\Validator\Constraints as Assert;

final class GenerateurPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        public ?int $annee_installation,
        public TypeGenerateur $type,
        public ?TypeVmc $type_vmc,
        public bool $presence_echangeur_thermique,
        public bool $generateur_collectif,
    ) {}
}
