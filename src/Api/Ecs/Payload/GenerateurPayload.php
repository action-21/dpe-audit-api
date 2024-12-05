<?php

namespace App\Api\Ecs\Payload;

use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class GenerateurPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        #[Assert\Uuid]
        public ?string $generateur_mixte_id,
        #[AppAssert\ReseauChaleur]
        public ?string $reseau_chaleur_id,
        public string $description,
        public TypeGenerateur $type,
        public EnergieGenerateur $energie,
        #[Assert\PositiveOrZero]
        public int $volume_stockage,
        public bool $position_volume_chauffe,
        public bool $generateur_collectif,
        #[Assert\Valid]
        public SignaletiquePayload $signaletique,
        public ?int $annee_installation,
    ) {}
}
