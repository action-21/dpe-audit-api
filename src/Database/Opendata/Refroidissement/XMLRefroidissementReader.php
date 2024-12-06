<?php

namespace App\Database\Opendata\Refroidissement;

use App\Database\Opendata\{XMLElement, XMLReader};

final class XMLRefroidissementReader extends XMLReader
{
    /** @return XMLClimatisationReader[] */
    public function read_climatisations(): array
    {
        return \array_map(
            fn(XMLElement $xml): XMLClimatisationReader => XMLClimatisationReader::from($xml),
            $this->xml()->findMany('.//climatisation_collection//climatisation')
        );
    }

    // * Données calculées

    public function besoin_fr(bool $scenario_depensier = false): float
    {
        return $scenario_depensier ? $this->xml()->findOneOrError('.//besoin_fr_depensier')->floatval() : $this->xml()->findOneOrError('.//besoin_fr')->floatval();
    }
}
