<?php

namespace App\Database\Opendata\Chauffage;

use App\Database\Opendata\{XMLElement, XMLReader};

final class XMLChauffageReader extends XMLReader
{
    /** @return XMLInstallationReader[] */
    public function read_installations(): array
    {
        return \array_map(
            fn(XMLElement $xml): XMLInstallationReader => XMLInstallationReader::from($xml),
            $this->xml()->findMany('.//installation_chauffage_collection//installation_chauffage')
        );
    }

    /** @return XMLGenerateurReader[] */
    public function read_generateurs(): array
    {
        return \array_filter(\array_map(
            fn(XMLElement $xml): XMLGenerateurReader => XMLGenerateurReader::from($xml),
            $this->xml()->findMany('.//generateur_chauffage_collection//generateur_chauffage')
        ), fn(XMLGenerateurReader $reader): bool => $reader->apply());
    }

    /** @return XMLEmetteurReader[] */
    public function read_emetteurs(): array
    {
        return \array_filter(\array_map(
            fn(XMLElement $xml): XMLEmetteurReader => XMLEmetteurReader::from($xml),
            $this->xml()->findMany('.//emetteur_chauffage_collection//emetteur_chauffage')
        ), fn(XMLEmetteurReader $reader): bool => $reader->apply());
    }

    // * Données calculées

    public function besoin_ch(bool $scenario_depensier = false): float
    {
        return $scenario_depensier ? $this->xml()->findOneOrError('.//besoin_ch_depensier')->floatval() : $this->xml()->findOneOrError('.//besoin_ch')->floatval();
    }
}
