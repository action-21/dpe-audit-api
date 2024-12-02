<?php

namespace App\Database\Opendata\Ecs;

use App\Database\Opendata\XMLReader;

final class XMLEcsReader extends XMLReader
{
    public function read_installations(): XMLInstallationReader
    {
        return XMLInstallationReader::from($this->xml()->findMany('.//installation_ecs_collection//installation_ecs'));
    }

    public function read_generateurs(): XMLGenerateurReader
    {
        return XMLGenerateurReader::from($this->xml()->findMany('.//generateur_ecs_collection//generateur_ecs'));
    }

    // * Données calculées

    public function besoin_ecs(bool $scenario_depensier = false): float
    {
        return $scenario_depensier ? $this->xml()->findOneOrError('.//besoin_ecs_depensier')->floatval() / 1000 : $this->xml()->findOneOrError('.//besoin_ecs')->floatval() / 1000;
    }
}
