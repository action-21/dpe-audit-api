<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\{XMLElement, XMLReader};

final class XMLVentilationReader extends XMLReader
{
    /** @return XMLInstallationReader[] */
    public function read_installations(): array
    {
        return \array_map(
            fn(XMLElement $xml): XMLInstallationReader => XMLInstallationReader::from($xml),
            $this->xml()->findMany('.//ventilation_collection//ventilation')
        );
    }
}
