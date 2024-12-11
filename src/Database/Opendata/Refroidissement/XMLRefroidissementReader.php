<?php

namespace App\Database\Opendata\Refroidissement;

use App\Database\Opendata\{XMLElement, XMLReader};

final class XMLRefroidissementReader extends XMLReader
{
    /** @return XMLGenerateurReader[] */
    public function read_generateurs(): array
    {
        return \array_map(
            fn(XMLElement $xml): XMLGenerateurReader => XMLGenerateurReader::from($xml),
            $this->xml()->findMany('.//climatisation_collection//climatisation')
        );
    }

    // * Données calculées

    public function besoin_fr(bool $scenario_depensier = false): float
    {
        return $scenario_depensier ? $this->xml()->findOneOrError('.//besoin_fr_depensier')->floatval() : $this->xml()->findOneOrError('.//besoin_fr')->floatval();
    }
}
