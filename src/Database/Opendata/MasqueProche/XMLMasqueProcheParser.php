<?php

namespace App\Database\Opendata\MasqueProche;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\MasqueProche\{MasqueProche, MasqueProcheCollection};

final class XMLMasqueProcheParser
{
    public function __construct(private XMLMasqueProcheReader $reader)
    {
    }

    public function parse(XMLElement $xml, Enveloppe &$enveloppe): MasqueProcheCollection
    {
        foreach ($this->reader->read($xml) as $reader) {
            $entity = new MasqueProche(
                id: $reader->id(),
                enveloppe: $enveloppe,
                description: $reader->description(),
                type_masque_proche: $reader->enum_type_masque_proche(),
                avancee: $reader->avancee(),
                orientation: $reader->orientation(),
            );
            $enveloppe->masque_proche_collection()->add($entity);
            $enveloppe->baie_collection()->find($reader->reference_baie())->attach_masque_proche($entity);
        }
        return $enveloppe->masque_proche_collection();
    }
}
