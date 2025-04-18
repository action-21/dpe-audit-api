<?php

namespace App\Database\Opendata\Chauffage;

use App\Database\Opendata\{XMLElement, XMLReader};

final class XMLChauffageReader extends XMLReader
{
    public static function from(XMLElement $xml): static
    {
        return parent::from(static::root($xml));
    }

    /**
     * Dédoublonnage des générateurs de chauffage
     * 
     * @return XMLGenerateurReader[]
     */
    public function generateurs(): array
    {
        $references = [];
        $readers = [];

        foreach ($this->findMany('.//generateur_chauffage_collection//generateur_chauffage') as $xml) {
            $reader = XMLGenerateurReader::from($xml);

            if (in_array($reader->reference(), $references)) {
                continue;
            }
            if (false === $reader->supports()) {
                continue;
            }
            $readers[] = $reader;
        }
        return $readers;
    }

    /**
     * Dédoublonnage des émetteurs de chauffage
     * 
     * Suppression des émetteurs directs considérés comme des systèmes sans émission
     * 
     * @return XMLEmetteurReader[]
     */
    public function emetteurs(): array
    {
        $references = [];
        $readers = [];

        foreach ($this->findMany('.//emetteur_chauffage_collection//emetteur_chauffage') as $xml) {
            $reader = XMLEmetteurReader::from($xml);

            if (in_array($reader->reference(), $references)) {
                continue;
            }
            if (false === $reader->supports()) {
                continue;
            }
            $readers[] = $reader;
        }
        return $readers;
    }

    /**
     * Les installations correspondant à un appoint électrique dans la salle de bain sont reconstituées
     * 
     * @return XMLInstallationReader[]
     */
    public function installations(): array
    {
        $readers = array_map(
            fn(XMLElement $xml): XMLInstallationReader => XMLInstallationReader::from($xml),
            $this->findMany('.//installation_chauffage_collection//installation_chauffage')
        );

        foreach ($readers as $reader) {
            if ($reader->has_appoint_electrique_sdb()) {
                $fake_installation_reader = XMLInstallationReader::from($reader->xml());
                $fake_installation_reader->is_appoint_electrique_sdb = true;
                $readers[] = $fake_installation_reader;
            }
        }
        return $readers;
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
            $this->findMany('.//generateur_chauffage_collection//generateur_chauffage')
        );
    }

    public function besoin_ch(bool $scenario_depensier = false): float
    {
        return $scenario_depensier ? $this->findOneOrError('.//besoin_ch_depensier')->floatval() : $this->findOneOrError('.//besoin_ch')->floatval();
    }
}
