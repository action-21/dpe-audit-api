<?php

namespace App\Domain\Chauffage\ValueObject\Signaletique;

use App\Domain\Chauffage\Enum\LabelGenerateur;
use App\Domain\Chauffage\ValueObject\Signaletique;

final class ChauffageElectrique extends Signaletique
{
    public static function create(LabelGenerateur\LabelGenerateurElectrique $label, ?float $pn,): static
    {
        return new self(label: $label->to(), pn: $pn);
    }
}
