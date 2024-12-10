<?php

namespace App\Api\Chauffage\Payload\Signaletique;

use App\Domain\Chauffage\Enum\{EnergieGenerateur, LabelGenerateur, TypeGenerateur};
use App\Domain\Chauffage\ValueObject\Signaletique;
use Symfony\Component\Validator\Constraints as Assert;

final class PoeleInsertPayload
{
    public function __construct(
        public TypeGenerateur\PoeleInsert $type,
        public EnergieGenerateur\PoeleInsert $energie,
        public LabelGenerateur\PoeleInsert $label,
        #[Assert\Positive]
        public ?float $pn,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_poele_insert(type: $this->type, energie: $this->energie, label: $this->label)
            ->with_pn($this->pn);
    }
}
