<?php

namespace App\Api\Ventilation\Payload;

use App\Api\Ventilation\Payload\Signaletique\{PuitClimatiquePayload, VentilationMecaniquePayload, VmcPayload, VmiPayload, VmrPayload};
use Symfony\Component\Validator\Constraints as Assert;

final class GenerateurPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        public ?int $annee_installation,
        public bool $generateur_collectif,
        #[Assert\Valid]
        public PuitClimatiquePayload|VentilationMecaniquePayload|VmcPayload|VmiPayload|VmrPayload $signaletique,
    ) {}
}
