<?php

namespace App\Api\Refroidissement\Payload\Signaletique;

use App\Domain\Refroidissement\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Refroidissement\ValueObject\Signaletique;
use Symfony\Component\Validator\Constraints as Assert;

final class ClimatiseurPayload implements SignaletiquePayload
{
    public function __construct(
        public TypeGenerateur\Climatiseur $type_generateur,
        public EnergieGenerateur\Climatiseur $energie_generateur,
        #[Assert\Positive]
        public ?float $seer,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_climatiseur(
            type_generateur: $this->type_generateur,
            energie_generateur: $this->energie_generateur,
        )->with_seer($this->seer);
    }
}
