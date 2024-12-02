<?php

namespace App\Database\Opendata\Refroidissement;

use App\Database\Opendata\XMLReader;

final class XMLRefroidissementReader extends XMLReader
{
    public function read_climatisations(): XMLClimatisationReader
    {
        return XMLClimatisationReader::from($this->xml()->findMany('.//climatisation_collection//climatisation'));
    }

    // * Données calculées

    public function besoin_fr(bool $scenario_depensier = false): float
    {
        return $scenario_depensier ? $this->xml()->findOneOrError('.//besoin_fr_depensier')->floatval() : $this->xml()->findOneOrError('.//besoin_fr')->floatval();
    }
}
