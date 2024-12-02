<?php

namespace App\Database\Opendata\Chauffage;

use App\Database\Opendata\XMLReader;

final class XMLChauffageReader extends XMLReader
{
    public function read_installations(): XMLInstallationReader
    {
        return XMLInstallationReader::from($this->xml()->findMany('.//installation_chauffage_collection//installation_chauffage'));
    }

    public function read_generateurs(): XMLGenerateurReader
    {
        return XMLGenerateurReader::from($this->xml()->findMany('.//generateur_chauffage_collection//generateur_chauffage'));
    }

    public function read_emetteurs(): XMLEmetteurReader
    {
        return XMLEmetteurReader::from($this->xml()->findMany('.//emetteur_chauffage_collection//emetteur_chauffage'));
    }

    // * Données calculées

    public function besoin_ch(bool $scenario_depensier = false): float
    {
        return $scenario_depensier ? $this->xml()->findOneOrError('.//besoin_ch_depensier')->floatval() : $this->xml()->findOneOrError('.//besoin_ch')->floatval();
    }
}
