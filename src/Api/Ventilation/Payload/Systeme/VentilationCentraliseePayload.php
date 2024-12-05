<?php

namespace App\Api\Ventilation\Payload\Systeme;

use App\Domain\Ventilation\Enum\{ModeExtraction, ModeInsufflation};
use Symfony\Component\Validator\Constraints as Assert;

final class VentilationCentraliseePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        #[Assert\Uuid]
        public string $generateur_id,
        public ModeExtraction $mode_extraction,
        public ModeInsufflation $mode_insufflation,
    ) {}
}
