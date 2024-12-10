<?php

namespace App\Api\Production\Payload;

use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class PanneauPhotovoltaiquePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        #[AppAssert\Orientation]
        public float $orientation,
        #[AppAssert\Inclinaison]
        public float $inclinaison,
        #[Assert\Positive]
        public int $modules,
        #[Assert\Positive]
        public ?float $surface_capteurs,
    ) {}
}
