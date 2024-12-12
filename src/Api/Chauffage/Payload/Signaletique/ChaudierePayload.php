<?php

namespace App\Api\Chauffage\Payload\Signaletique;

use App\Api\Chauffage\Payload\CombustionPayload;
use App\Domain\Chauffage\Enum\{EnergieGenerateur, PositionChaudiere, TypeGenerateur};
use App\Domain\Chauffage\ValueObject\Signaletique;
use Symfony\Component\Validator\Constraints as Assert;

final class ChaudierePayload implements SignaletiquePayload
{
    public function __construct(
        public TypeGenerateur\Chaudiere $type,
        public EnergieGenerateur\Chaudiere $energie,
        public PositionChaudiere $position,
        #[Assert\Valid]
        public CombustionPayload $combustion,
        #[Assert\PositiveOrZero]
        #[Assert\LessThanOrEqual(2)]
        public ?int $priorite_cascade,
        #[Assert\Positive]
        public ?float $pn,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_chaudiere(type: $this->type, energie: $this->energie)
            ->with_pn($this->pn)
            ->with_position($this->position)
            ->with_combustion($this->combustion->to())
            ->with_cascade($this->priorite_cascade);
    }
}
