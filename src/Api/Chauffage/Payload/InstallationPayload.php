<?php

namespace App\Api\Chauffage\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class InstallationPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        #[Assert\Positive]
        public float $surface,
        public bool $comptage_individuel,
        #[Assert\Valid]
        public ?SolairePayload $solaire,
        #[Assert\Valid]
        public RegulationPayload $regulation_centrale,
        #[Assert\Valid]
        public RegulationPayload $regulation_terminale,
        #[Assert\All([new Assert\Type(SystemePayload::class)])]
        #[Assert\Count(min: 1)]
        #[Assert\Valid]
        public array $systemes,
    ) {}
}
