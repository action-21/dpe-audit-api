<?php

namespace App\Domain\Chauffage\ValueObject\Signaletique;

use App\Domain\Chauffage\Enum\LabelGenerateur;
use App\Domain\Chauffage\ValueObject\Signaletique;

final class PoeleInsertBois extends Signaletique
{
    public static function create(LabelGenerateur\LabelGenerateurBois $label, ?float $pn,): static
    {
        return new self(label: $label->to(), pn: $pn);
    }
}
