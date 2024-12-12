<?php

namespace App\Api\Chauffage\Payload\Signaletique;

use App\Api\Chauffage\Payload\CombustionPayload;
use App\Domain\Chauffage\Enum\{EnergieGenerateur, PositionChaudiere, TypeGenerateur};
use App\Domain\Chauffage\ValueObject\Signaletique;
use Symfony\Component\Validator\Constraints as Assert;

final class PacHybridePayload implements SignaletiquePayload
{
    public function __construct(
        public TypeGenerateur\PacHybride $type,
        public EnergieGenerateur\PacHybride $energie_partie_chaudiere,
        public PositionChaudiere $position,
        #[Assert\Valid]
        public CombustionPayload $combustion,
        #[Assert\PositiveOrZero]
        #[Assert\LessThanOrEqual(2)]
        public ?int $priorite_cascade,
        #[Assert\Positive]
        public ?float $pn,
        #[Assert\Positive]
        public ?float $scop,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_pac_hybride(type: $this->type, energie_partie_chaudiere: $this->energie_partie_chaudiere)
            ->with_pn($this->pn)
            ->with_scop($this->scop)
            ->with_position($this->position)
            ->with_combustion($this->combustion->to())
            ->with_cascade($this->priorite_cascade);
    }
}
