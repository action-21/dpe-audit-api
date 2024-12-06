<?php

namespace App\Database\Opendata\Visite;

use App\Database\Opendata\{XMLElement, XMLReader};

final class XMLVisiteReader extends XMLReader
{
    /** @return XMLLogementReader[] */
    public function read_logements(): array
    {
        return \array_map(
            fn(XMLElement $xml): XMLLogementReader => XMLLogementReader::from($xml),
            $this->xml()->findMany('.//logement_visite_collection//logement_visite')
        );
    }
}
