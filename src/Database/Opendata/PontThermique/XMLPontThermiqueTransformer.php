<?php

namespace App\Database\Opendata\PontThermique;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\PontThermique\{PontThermique, PontThermiqueCollection};

final class XMLPontThermiqueTransformer
{
    public function transform(XMLElement $root, Enveloppe $enveloppe): PontThermiqueCollection
    {
        foreach ($root->read_enveloppe()->read_ponts_thermiques() as $reader) {
            $entity = new PontThermique(
                id: $reader->id(),
                enveloppe: $enveloppe,
                description: $reader->description(),
                longueur: $reader->longueur(),
                liaison: $reader->liaison(),
                kpt: $reader->k_saisi(),
            );
            $enveloppe->add_pont_thermique($entity);
        }
        return $enveloppe->ponts_thermiques();
    }
}
