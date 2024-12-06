<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\{XMLElement, XMLReader};

final class XMLVentilationReader extends XMLReader
{
    /** @return XMLGenerateurReader[] */
    public function read_generateurs(): array
    {
        $readers = \array_map(
            fn(XMLElement $xml): XMLGenerateurReader => XMLGenerateurReader::from($xml),
            $this->xml()->findMany('.//ventilation_collection//ventilation')
        );

        return \array_filter($readers, fn(XMLGenerateurReader $reader): bool => $reader->apply());
    }

    /** @return XMLInstallationReader[] */
    public function read_installations(): array
    {
        return \array_map(
            fn(XMLElement $xml): XMLInstallationReader => XMLInstallationReader::from($xml),
            $this->xml()->findMany('.//ventilation_collection//ventilation')
        );
    }
}
