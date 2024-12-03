<?php

namespace App\Domain\Porte;

use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Porte\ValueObject\{Caracteristique, Position};
use Webmozart\Assert\Assert;

final class PorteFactory
{
    public function build(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        Position $position,
        Caracteristique $caracteristique,
    ): Porte {
        Assert::greaterThanEq($caracteristique->annee_installation, $enveloppe->annee_construction_batiment());

        return new Porte(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            position: $position,
            caracteristique: $caracteristique,
        );
    }
}
