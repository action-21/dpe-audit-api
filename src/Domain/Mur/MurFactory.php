<?php

namespace App\Domain\Mur;

use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Mur\ValueObject\{Caracteristique, Isolation, Position};
use Webmozart\Assert\Assert;

final class MurFactory
{
    public function build(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        Position $position,
        Caracteristique $caracteristique,
        Isolation $isolation,
    ): Mur {
        Assert::greaterThanEq($caracteristique->annee_construction, $enveloppe->annee_construction_batiment());
        Assert::greaterThanEq($caracteristique->annee_renovation, $enveloppe->annee_construction_batiment());

        return new Mur(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            position: $position,
            caracteristique: $caracteristique,
            isolation: $isolation,
        );
    }
}
