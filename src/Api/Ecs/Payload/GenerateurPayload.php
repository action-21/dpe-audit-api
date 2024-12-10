<?php

namespace App\Api\Ecs\Payload;

use App\Api\Ecs\Payload\Signaletique\{ChaudierePayload, ChauffeEauPayload, PacPayload, PoeleBouilleurPayload, ReseauChaleurPayload};
use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class GenerateurPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        #[AppAssert\ReseauChaleur]
        public ?string $reseau_chaleur_id,
        public string $description,
        public bool $position_volume_chauffe,
        public bool $generateur_collectif,
        #[Assert\Valid]
        public ChaudierePayload|ChauffeEauPayload|PacPayload|PoeleBouilleurPayload|ReseauChaleurPayload $signaletique,
        public ?int $annee_installation,
    ) {}
}
