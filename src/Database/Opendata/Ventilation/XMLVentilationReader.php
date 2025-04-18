<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\{XMLElement, XMLReader};

final class XMLVentilationReader extends XMLReader
{
    public static function from(XMLElement $xml): static
    {
        return parent::from(static::root($xml));
    }

    /** @return XMLGenerateurReader[] */
    public function generateurs(): array
    {
        return \array_filter(\array_map(
            fn(XMLElement $xml): XMLGenerateurReader => XMLGenerateurReader::from($xml),
            $this->findMany('.//ventilation_collection//ventilation')
        ), fn(XMLGenerateurReader $reader): bool => $reader->supports());
    }

    /** @return XMLInstallationReader[] */
    public function installations(): array
    {
        return \array_map(
            fn(XMLElement $xml): XMLInstallationReader => XMLInstallationReader::from($xml),
            $this->findMany('.//ventilation_collection//ventilation')
        );
    }

    /**
     * Reconstitution des systèmes à partir des installations
     * 
     * @return XMLSystemeReader[]
     */
    public function systemes(): array
    {
        return array_map(
            fn(XMLInstallationReader $reader): XMLSystemeReader => XMLSystemeReader::from($reader->xml()),
            $this->installations(),
        );
    }
}
