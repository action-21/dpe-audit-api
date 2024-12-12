<?php

namespace App\Api\Baie\Payload;

use App\Domain\Baie\Enum\TypeMasqueProche;
use Symfony\Component\Validator\Constraints as Assert;

final class MasqueProchePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        public TypeMasqueProche $type,
        #[Assert\Positive]
        public ?float $avancee,
    ) {}

    public function isValid(): bool
    {
        if (\in_array($this->type, [
            TypeMasqueProche::FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS,
            TypeMasqueProche::BALCON_OU_AUVENT
        ])) {
            return $this->avancee !== null;
        }
        return true;
    }
}
