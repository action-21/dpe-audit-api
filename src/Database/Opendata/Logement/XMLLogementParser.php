<?php

namespace App\Database\Opendata\Logement;

use App\Database\Opendata\Climatisation\XMLInstallationClimatisationParser;
use App\Database\Opendata\Ecs\XMLInstallationEcsParser;
use App\Database\Opendata\Ventilation\XMLInstallationVentilationParser;
use App\Database\Opendata\XMLElement;
use App\Domain\Batiment\Batiment;
use App\Domain\Logement\Entity\{Etage, EtageCollection};
use App\Domain\Logement\{Logement, LogementCollection};

final class XMLLogementParser
{
    public function __construct(
        private XMLLogementReader $reader,
        private XMLInstallationVentilationParser $installation_ventilation_parser,
        private XMLInstallationClimatisationParser $installation_climatisation_parser,
        private XMLInstallationEcsParser $installation_ecs_parser,
    ) {
    }

    public function parse(XMLElement $xml, Batiment &$batiment): LogementCollection
    {
        foreach ($this->reader->read($xml) as $reader) {
            $aggregate = new Logement(
                id: $reader->id(),
                batiment: $batiment,
                description: $reader->description(),
                etage_collection: new EtageCollection,
            );

            foreach ($reader->niveau_reader() as $niveau_reader) {
                $entity = new Etage(
                    id: $niveau_reader->id(),
                    logement: $aggregate,
                    description: $niveau_reader->description(),
                    surface_habitable: $niveau_reader->surface(),
                    hauteur_sous_plafond: $niveau_reader->hauteur_sous_plafond(),
                );
                $aggregate->add_etage($entity);
            }
            $this->installation_ventilation_parser->parse($reader->get(), $aggregate);
            $this->installation_climatisation_parser->parse($reader->get(), $aggregate);
            $this->installation_ecs_parser->parse($reader->get(), $aggregate);
            $batiment->add_logement($aggregate);
        }
        return $batiment->logement_collection();
    }
}
