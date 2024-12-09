<?php

namespace App\Api\Ecs\Payload\Signaletique;

use App\Api\Ecs\Payload\CombustionPayload;
use App\Domain\Ecs\Enum\{EnergieGenerateur, PositionChaudiere, TypeGenerateur};
use App\Domain\Ecs\ValueObject\Signaletique;
use Symfony\Component\Validator\Constraints as Assert;

final class ChaudierePayload
{
    public function __construct(
        public TypeGenerateur\Chaudiere $type,
        public EnergieGenerateur\Chaudiere $energie,
        #[Assert\PositiveOrZero]
        public int $volume_stockage,
        public bool $position_volume_chauffe,
        public bool $generateur_collectif,
        public PositionChaudiere $position,
        #[Assert\Positive]
        public ?float $pn,
        #[Assert\Valid]
        public ?CombustionPayload $combustion,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_chaudiere(
            type: $this->type,
            energie: $this->energie,
            volume_stockage: $this->volume_stockage,
            position_volume_chauffe: $this->position_volume_chauffe,
            generateur_collectif: $this->generateur_collectif,
            pn: $this->pn,
            position: $this->position,
            combustion: $this->combustion?->to(),
        );
    }
}
