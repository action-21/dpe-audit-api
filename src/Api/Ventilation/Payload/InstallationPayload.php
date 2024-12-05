<?php

namespace App\Api\Ventilation\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class InstallationPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        #[Assert\Positive]
        public float $surface,

        /** @var VentilationNaturellePayload[] */
        #[Assert\All([new Assert\Type(VentilationNaturellePayload::class,)])]
        #[Assert\Valid]
        public array $ventilations_naturelles,

        /** @var VentilationMequaniquePayload[] */
        #[Assert\All([new Assert\Type(VentilationMequaniquePayload::class,)])]
        #[Assert\Valid]
        public array $ventilations_mecaniques,
    ) {}

    #[Assert\Count(min: 1)]
    public function getSystemes(): array
    {
        return [
            ...$this->ventilations_mecaniques,
            ...$this->ventilations_naturelles,
        ];
    }
}
