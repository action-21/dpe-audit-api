<?php

namespace App\Api\Ecs\Payload\Signaletique;

use App\Domain\Ecs\Enum\{PositionChaudiere, TypeGenerateur};
use App\Domain\Ecs\ValueObject\Signaletique;
use Symfony\Component\Validator\Constraints as Assert;

final class PacPayload
{
    public function __construct(
        public TypeGenerateur\Pac $type,
        #[Assert\PositiveOrZero]
        public int $volume_stockage,
        public bool $position_volume_chauffe,
        public bool $generateur_collectif,
        public PositionChaudiere $position,
        #[Assert\Positive]
        public ?float $pn,
        #[Assert\Positive]
        public ?float $cop,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_pac(
            type: $this->type,
            volume_stockage: $this->volume_stockage,
            position_volume_chauffe: $this->position_volume_chauffe,
            generateur_collectif: $this->generateur_collectif,
            pn: $this->pn,
            cop: $this->cop,
        );
    }
}
