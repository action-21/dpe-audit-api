<?php

namespace App\Api\Ventilation\Payload;

use App\Domain\Ventilation\Enum\TypeVentilation;
use Symfony\Component\Validator\Constraints as Assert;

final class VentilationNaturellePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public TypeVentilation\TypeVentilationNaturelle $type_ventilation,
    ) {}
}
