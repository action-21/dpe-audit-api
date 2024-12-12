<?php

namespace App\Api\Ecs\Payload\Signaletique;

use App\Domain\Ecs\Enum\{PositionChaudiere, TypeGenerateur};
use App\Domain\Ecs\ValueObject\Signaletique;
use Symfony\Component\Validator\Constraints as Assert;

final class PacPayload implements SignaletiquePayload
{
    public function __construct(
        public TypeGenerateur\Pac $type,
        #[Assert\PositiveOrZero]
        public int $volume_stockage,
        public PositionChaudiere $position,
        #[Assert\Positive]
        public ?float $pn,
        #[Assert\Positive]
        public ?float $cop,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_pac(type: $this->type, volume_stockage: $this->volume_stockage,)
            ->with_pn($this->pn)
            ->with_cop($this->cop);
    }
}
