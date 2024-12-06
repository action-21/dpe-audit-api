<?php

namespace App\Database\Opendata\Production;

use App\Database\Opendata\{XMLElement, XMLReader};

final class XMLProductionReader extends XMLReader
{
    /** @return XMLPanneauPvReader[] */
    public function read_panneaux_photovoltaiques(): array
    {
        return \array_map(
            fn(XMLElement $xml): XMLPanneauPvReader => XMLPanneauPvReader::from($xml),
            $this->xml()->findMany('.//panneaux_pv_collection//panneaux_pv')
        );
    }
}
