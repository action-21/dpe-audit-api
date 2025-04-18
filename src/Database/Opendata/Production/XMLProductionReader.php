<?php

namespace App\Database\Opendata\Production;

use App\Database\Opendata\{XMLElement, XMLReader};

final class XMLProductionReader extends XMLReader
{
    public static function from(XMLElement $xml): static
    {
        return parent::from(static::root($xml));
    }

    /** @return XMLPanneauPvReader[] */
    public function panneaux_photovoltaiques(): array
    {
        return array_filter(
            array_map(
                fn(XMLElement $xml): XMLPanneauPvReader => XMLPanneauPvReader::from($xml),
                $this->findMany('.//panneaux_pv_collection//panneaux_pv'),
            ),
            fn(XMLPanneauPvReader $reader): bool => $reader->supports(),
        );
    }
}
