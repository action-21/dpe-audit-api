<?php

namespace App\Domain\PlancherHaut;

use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\PlancherHaut\ValueObject\{Caracteristique, Isolation, Position};
use Webmozart\Assert\Assert;

final class PlancherHautFactory
{
    public function build(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        Position $position,
        Caracteristique $caracteristique,
        Isolation $isolation,
    ): PlancherHaut {
        Assert::greaterThanEq($caracteristique->annee_construction, $enveloppe->annee_construction_batiment());
        Assert::greaterThanEq($caracteristique->annee_renovation, $enveloppe->annee_construction_batiment());

        return new PlancherHaut(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            position: $position,
            caracteristique: $caracteristique,
            isolation: $isolation,
        );
    }
}
