<?php

namespace App\Api\Ecs\Payload\Signaletique;

use App\Api\Ecs\Payload\CombustionPayload;
use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Ecs\ValueObject\Signaletique;
use Symfony\Component\Validator\Constraints as Assert;

final class PoeleBouilleurPayload implements SignaletiquePayload
{
    public function __construct(
        public TypeGenerateur\PoeleBouilleur $type,
        public EnergieGenerateur\PoeleBouilleur $energie,
        #[Assert\PositiveOrZero]
        public int $volume_stockage,
        #[Assert\Positive]
        public ?float $pn,
        #[Assert\Valid]
        public ?CombustionPayload $combustion,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_poele_bouilleur(type: $this->type, energie: $this->energie, volume_stockage: $this->volume_stockage,)
            ->with_pn($this->pn)
            ->with_combustion($this->combustion?->to());
    }
}
