<?php

namespace App\Api\Chauffage\Payload\Signaletique;

use App\Domain\Chauffage\Enum\{LabelGenerateur, TypeGenerateur};
use App\Domain\Chauffage\ValueObject\Signaletique;
use Symfony\Component\Validator\Constraints as Assert;

final class ChauffageElectriquePayload
{
    public function __construct(
        public TypeGenerateur\ChauffageElectrique $type,
        public LabelGenerateur\ChauffageElectrique $label,
        #[Assert\Positive]
        public ?float $pn,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_chauffage_electrique(type: $this->type, label: $this->label)
            ->with_pn($this->pn);
    }
}
