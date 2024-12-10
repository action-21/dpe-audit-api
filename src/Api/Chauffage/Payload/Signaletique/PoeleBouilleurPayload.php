<?php

namespace App\Api\Chauffage\Payload\Signaletique;

use App\Api\Chauffage\Payload\CombustionPayload;
use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Chauffage\ValueObject\Signaletique;
use Symfony\Component\Validator\Constraints as Assert;

final class PoeleBouilleurPayload
{
    public function __construct(
        public TypeGenerateur\PoeleBouilleur $type,
        public EnergieGenerateur\PoeleBouilleur $energie,
        #[Assert\Valid]
        public CombustionPayload $combustion,
        #[Assert\Positive]
        public ?float $pn,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_poele_bouilleur(type: $this->type, energie: $this->energie)
            ->with_combustion($this->combustion->to())
            ->with_pn($this->pn);
    }
}
