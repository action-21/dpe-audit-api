<?php

namespace App\Api\Refroidissement\Payload\Signaletique;

use App\Domain\Refroidissement\Enum\TypeGenerateur;
use App\Domain\Refroidissement\ValueObject\Signaletique;
use Symfony\Component\Validator\Constraints as Assert;

final class PacPayload implements SignaletiquePayload
{
    public function __construct(
        public TypeGenerateur\Pac $type_generateur,
        #[Assert\Positive]
        public ?float $seer,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_pac(
            type_generateur: $this->type_generateur,
        )->with_seer($this->seer);
    }
}
