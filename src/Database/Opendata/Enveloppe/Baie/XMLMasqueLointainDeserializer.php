<?php

namespace App\Database\Opendata\Enveloppe\Baie;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Data\Baie\MasqueLointainData;
use App\Domain\Enveloppe\Entity\Baie;
use App\Domain\Enveloppe\Entity\Baie\MasqueLointain;

final class XMLMasqueLointainDeserializer
{
    private XMLMasqueLointainReader $reader;

    public function deserialize(XMLElement $xml, Baie $entity): MasqueLointain
    {
        $this->reader = XMLMasqueLointainReader::from($xml);

        return new MasqueLointain(
            id: $this->reader->id(),
            baie: $entity,
            description: $this->reader->description(),
            type_masque: $this->reader->type_masque(),
            hauteur: $this->reader->hauteur(),
            orientation: $this->reader->orientation(),
            data: MasqueLointainData::create(),
        );
    }
}
