<?php

namespace App\Api\Chauffage\Payload;

use App\Domain\Chauffage\Enum\{TemperatureDistribution, TypeEmetteur};
use Symfony\Component\Validator\Constraints as Assert;

final class EmetteurPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        public TypeEmetteur $type,
        public TemperatureDistribution $temperature_distribution,
        public bool $presence_robinet_thermostatique,
        public ?int $annee_installation,
    ) {}
}
