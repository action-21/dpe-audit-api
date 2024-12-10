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
        #[Assert\Positive]
        public ?float $pn,
        public ?LabelGenerateur $label,
        #[Assert\Valid]
        public ?CombustionPayload $combustion,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_chauffe_eau(type: $this->type, energie: $this->energie, volume_stockage: $this->volume_stockage,)
            ->with_pn($this->pn)
            ->with_label($this->label)
            ->with_combustion($this->combustion?->to());
    }
}
