<?php

namespace App\Api\Chauffage\Payload\Signaletique;

use App\Api\Chauffage\Payload\CombustionPayload;
use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Chauffage\ValueObject\Signaletique;
use Symfony\Component\Validator\Constraints as Assert;

final class GenerateurAirChaudPayload implements SignaletiquePayload
{
    public function __construct(
        public TypeGenerateur\GenerateurAirChaud $type,
        public EnergieGenerateur\GenerateurAirChaud $energie,
        #[Assert\Valid]
        public ?CombustionPayload $combustion,
        #[Assert\Positive]
        public ?float $pn,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_generateur_air_chaud(type: $this->type, energie: $this->energie)
            ->with_pn($this->pn)
            ->with_combustion($this->combustion?->to());
    }
}
