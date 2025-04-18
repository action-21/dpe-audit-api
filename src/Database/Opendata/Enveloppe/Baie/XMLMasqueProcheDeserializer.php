<?php

namespace App\Database\Opendata\Enveloppe\Baie;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Data\Baie\MasqueProcheData;
use App\Domain\Enveloppe\Entity\Baie;
use App\Domain\Enveloppe\Entity\Baie\MasqueProche;

final class XMLMasqueProcheDeserializer
{
    private XMLMasqueProcheReader $reader;

    public function deserialize(XMLElement $xml, Baie $entity): MasqueProche
    {
        $this->reader = XMLMasqueProcheReader::from($xml);

        return new MasqueProche(
            id: $this->reader->id(),
            baie: $entity,
            description: $this->reader->description(),
            type_masque: $this->reader->type_masque(),
            profondeur: $this->reader->profondeur(),
            data: MasqueProcheData::create(),
        );
    }
}
