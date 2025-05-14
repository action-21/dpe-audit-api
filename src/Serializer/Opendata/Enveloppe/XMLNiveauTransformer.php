<?php

namespace App\Serializer\Opendata\Enveloppe;

use App\Database\Opendata\Enveloppe\Niveau\XMLNiveauReader;
use App\Domain\Enveloppe\Entity\Niveau;
use App\Domain\Enveloppe\Enveloppe;

final class XMLNiveauTransformer
{
    public function to(XMLNiveauReader $reader, Enveloppe $entity): Niveau
    {
        return Niveau::create(
            id: $reader->id(),
            enveloppe: $entity,
            surface: $reader->surface(),
            inertie_paroi_verticale: $reader->inertie_paroi_verticale(),
            inertie_plancher_haut: $reader->inertie_plancher_haut(),
            inertie_plancher_bas: $reader->inertie_plancher_bas(),
        );
    }
}
