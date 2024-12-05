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
        /** @var (Systeme\VentilationCentraliseePayload|Systeme\VentilationDiviseePayload|Systeme\VentilationNaturellePayload)[] */
        #[Assert\All([new Assert\Type([
            Systeme\VentilationCentraliseePayload::class,
            Systeme\VentilationDiviseePayload::class,
            Systeme\VentilationNaturellePayload::class,
        ])])]
        #[Assert\Count(min: 1)]
        #[Assert\Valid]
        public array $systemes,
    ) {}
}
