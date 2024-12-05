<?php

namespace App\Api\Ventilation\Payload;

use App\Api\Ventilation\Payload\Systeme;
use Symfony\Component\Validator\Constraints as Assert;

final class InstallationPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        #[Assert\Positive]
        public float $surface,

        /** @var Systeme\VentilationCentraliseePayload[] */
        #[Assert\All([new Assert\Type(Systeme\VentilationCentraliseePayload::class,)])]
        #[Assert\Valid]
        public array $systemes_mecaniques_centralises,

        /** @var Systeme\VentilationDiviseePayload[] */
        #[Assert\All([new Assert\Type(Systeme\VentilationDiviseePayload::class,)])]
        #[Assert\Valid]
        public array $systemes_mecaniques_divises,

        /** @var Systeme\VentilationNaturellePayload[] */
        #[Assert\All([new Assert\Type(Systeme\VentilationNaturellePayload::class,)])]
        #[Assert\Valid]
        public array $systemes_naturels,
    ) {}

    #[Assert\Count(min: 1)]
    public function getSystemes(): array
    {
        return [
            ...$this->systemes_mecaniques_centralises,
            ...$this->systemes_mecaniques_divises,
            ...$this->systemes_naturels,
        ];
    }
}
