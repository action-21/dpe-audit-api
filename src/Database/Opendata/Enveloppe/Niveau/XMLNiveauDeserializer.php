<?php

namespace App\Database\Opendata\Enveloppe\Niveau;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Data\NiveauData;
use App\Domain\Enveloppe\Entity\Niveau;
use App\Domain\Enveloppe\Enveloppe;

final class XMLNiveauDeserializer
{
    public function deserialize(XMLElement $xml, Enveloppe $entity): Niveau
    {
        $reader = XMLNiveauReader::from($xml);
        return new Niveau(
            id: $reader->id(),
            enveloppe: $entity,
            surface: $reader->surface(),
            inertie_paroi_verticale: $reader->inertie_paroi_verticale(),
            inertie_plancher_haut: $reader->inertie_plancher_haut(),
            inertie_plancher_bas: $reader->inertie_plancher_bas(),
            data: NiveauData::create(),
        );
    }
}
