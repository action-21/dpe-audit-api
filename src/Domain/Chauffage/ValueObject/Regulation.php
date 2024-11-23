<?php

namespace App\Domain\Chauffage\ValueObject;

final class Regulation
{
    public function __construct(
        public readonly bool $presence_regulation,
        public readonly bool $minimum_temperature,
        public readonly bool $detection_presence,
    ) {}

    public static function create(
        bool $presence_regulation = false,
        bool $minimum_temperature = false,
        bool $detection_presence = false,
    ): self {
        return new self(
            presence_regulation: $presence_regulation,
            minimum_temperature: $presence_regulation && $minimum_temperature,
            detection_presence: $presence_regulation && $detection_presence,
        );
    }

    public function intermittence(): bool
    {
        return $this->minimum_temperature || $this->detection_presence;
    }
}
