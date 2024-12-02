<?php

namespace App\Database\Opendata\Production;

use App\Database\Opendata\XMLReader;

final class XMLProductionReader extends XMLReader
{
    public function read_panneaux_photovoltaiques(): XMLPanneauPvReader
    {
        return XMLPanneauPvReader::from($this->xml()->findMany('.//panneaux_pv_collection//panneaux_pv'));
    }
}