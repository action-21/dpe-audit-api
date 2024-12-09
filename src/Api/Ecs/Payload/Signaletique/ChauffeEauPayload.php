<?php

namespace App\Api\Ecs\Payload\Signaletique;

use App\Api\Ecs\Payload\CombustionPayload;
use App\Domain\Ecs\Enum\{EnergieGenerateur, LabelGenerateur, TypeGenerateur};
use App\Domain\Ecs\ValueObject\Signaletique;
use Symfony\Component\Validator\Constraints as Assert;

final class ChauffeEauPayload
{
    public function __construct(
        public TypeGenerateur\ChauffeEau $type,
        public EnergieGenerateur\ChauffeEau $energie,
        #[Assert\PositiveOrZero]
        public int $volume_stockage,
        public bool $position_volume_chauffe,
        public bool $generateur_collectif,
        #[Assert\Positive]
        public ?float $pn,
        public ?LabelGenerateur $label,
        #[Assert\Valid]
        public ?CombustionPayload $combustion,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_chauffe_eau(
            type: $this->type,
            energie: $this->energie,
            volume_stockage: $this->volume_stockage,
            position_volume_chauffe: $this->position_volume_chauffe,
            generateur_collectif: $this->generateur_collectif,
            pn: $this->pn,
            label: $this->label,
            combustion: $this->combustion?->to(),
        );
    }
}
