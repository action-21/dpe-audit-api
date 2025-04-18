<?php

namespace App\Database\Opendata\Refroidissement;

use App\Database\Opendata\{XMLElement, XMLReader};

final class XMLRefroidissementReader extends XMLReader
{
    public static function from(XMLElement $xml): static
    {
        return parent::from(static::root($xml));
    }

    /** @return XMLGenerateurReader[] */
    public function generateurs(): array
    {
        return \array_map(
            fn(XMLElement $xml): XMLGenerateurReader => XMLGenerateurReader::from($xml),
            $this->findMany('.//climatisation_collection//climatisation')
        );
    }

    /**
     * Reconstitution des installations à partir des générateurs
     * 
     * @return XMLInstallationReader[]
     */
    public function installations(): array
    {
        return \array_map(
            fn(XMLElement $xml): XMLInstallationReader => XMLInstallationReader::from($xml),
            $this->findMany('.//climatisation_collection//climatisation')
        );
    }

    /**
     * Reconstitution des installations à partir des générateurs
     * 
     * @return XMLSystemeReader[]
     */
    public function systemes(): array
    {
        return \array_map(
            fn(XMLElement $xml): XMLSystemeReader => XMLSystemeReader::from($xml),
            $this->findMany('.//climatisation_collection//climatisation')
        );
    }

    public function besoin_fr(bool $scenario_depensier = false): float
    {
        return $scenario_depensier
            ? $this->findOneOrError('.//besoin_fr_depensier')->floatval()
            : $this->findOneOrError('.//besoin_fr')->floatval();
    }
}
