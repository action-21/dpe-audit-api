<?php

namespace App\Api\Chauffage\Payload\Signaletique;

use App\Domain\Chauffage\Enum\TypeGenerateur;
use App\Domain\Chauffage\ValueObject\Signaletique;
use Symfony\Component\Validator\Constraints as Assert;

final class PacPayload
{
    public function __construct(
        public TypeGenerateur\Pac $type,
        #[Assert\Positive]
        public ?float $pn,
        #[Assert\Positive]
        public ?float $scop,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_pac(type: $this->type)
            ->with_pn($this->pn)
            ->with_scop($this->scop);
    }
}
