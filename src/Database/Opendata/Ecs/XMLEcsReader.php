<?php

namespace App\Database\Opendata\Ecs;

use App\Database\Opendata\{XMLElement, XMLReader};

final class XMLEcsReader extends XMLReader
{
    public static function from(XMLElement $xml): static
    {
        return parent::from(static::root($xml));
    }

    /**
     * @return XMLGenerateurReader[]
     */
    public function generateurs(): array
    {
        $references = [];
        $readers = [];

        foreach ($this->findMany('.//generateur_ecs_collection//generateur_ecs') as $xml) {
            $reader = XMLGenerateurReader::from($xml);

            if (in_array($reader->reference(), $references)) {
                continue;
            }
            $readers[] = $reader;
        }
        return $readers;
    }

    /**
     * @return XMLInstallationReader[]
     */
    public function installations(): array
    {
        return \array_map(
            fn(XMLElement $xml): XMLInstallationReader => XMLInstallationReader::from($xml),
            $this->findMany('.//installation_ecs_collection//installation_ecs')
        );
    }

    /**
     * Reconstitution des systèmes d'ECS depuis chaque générateur
     * 
     * @return XMLSystemeReader[]
     */
    public function systemes(): array
    {
        return array_map(
            fn(XMLElement $xml): XMLSystemeReader => XMLSystemeReader::from($xml),
            $this->findMany('.//generateur_ecs_collection//generateur_ecs')
        );
    }

    // * Données calculées

    public function besoin_ecs(bool $scenario_depensier = false): float
    {
        return $scenario_depensier ? $this->findOneOrError('.//besoin_ecs_depensier')->floatval() / 1000 : $this->findOneOrError('.//besoin_ecs')->floatval() / 1000;
    }
}
