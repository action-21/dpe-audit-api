<?php

namespace App\Api\Chauffage\Payload;

use App\Domain\Chauffage\ValueObject\Regulation;

final class RegulationPayload
{
    public function __construct(
        public bool $presence_regulation,
        public bool $minimum_temperature,
        public bool $detection_presence,
    ) {}

    public function to(): Regulation
    {
        return Regulation::create(
            presence_regulation: $this->presence_regulation,
            minimum_temperature: $this->minimum_temperature,
            detection_presence: $this->detection_presence,
        );
    }
}
