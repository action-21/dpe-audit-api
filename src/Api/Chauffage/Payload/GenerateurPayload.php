<?php

namespace App\Api\Chauffage\Payload;

use App\Api\Chauffage\Payload\Signaletique\{ChaudierePayload, ChauffageElectriquePayload, GenerateurAirChaudPayload, PacHybridePayload, PacPayload, PoeleBouilleurPayload, PoeleInsertPayload, RadiateurGazPayload, ReseauChaleurPayload};
use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class GenerateurPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        #[Assert\Uuid]
        public ?string $generateur_mixte_id,
        #[AppAssert\ReseauChaleur]
        public ?string $reseau_chaleur_id,
        public ?int $annee_installation,
        public bool $position_volume_chauffe,
        public bool $generateur_collectif,
        #[Assert\Valid]
        public ChaudierePayload|ChauffageElectriquePayload|GenerateurAirChaudPayload|PacHybridePayload|PacPayload|PoeleBouilleurPayload|PoeleInsertPayload|RadiateurGazPayload|ReseauChaleurPayload $signaletique,
    ) {}
}
