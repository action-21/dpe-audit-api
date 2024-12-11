<?php

namespace App\Database\Opendata\Ecs;

use App\Database\Opendata\{XMLElement, XMLReader};

final class XMLEcsReader extends XMLReader
{
    /** @return XMLInstallationReader[] */
    public function read_installations(): array
    {
        return \array_map(
            fn(XMLElement $xml): XMLInstallationReader => XMLInstallationReader::from($xml),
            $this->xml()->findMany('.//installation_ecs_collection//installation_ecs')
        );
    }

    /** @return XMLGenerateurReader[] */
    public function read_generateurs(): array
    {
        return \array_filter(\array_map(
            fn(XMLElement $xml): XMLGenerateurReader => XMLGenerateurReader::from($xml),
            $this->xml()->findMany('.//generateur_ecs_collection//generateur_ecs')
        ), fn(XMLGenerateurReader $reader): bool => $reader->apply());
    }

    // * Données calculées

    public function besoin_ecs(bool $scenario_depensier = false): float
    {
        return $scenario_depensier ? $this->xml()->findOneOrError('.//besoin_ecs_depensier')->floatval() / 1000 : $this->xml()->findOneOrError('.//besoin_ecs')->floatval() / 1000;
    }
}
