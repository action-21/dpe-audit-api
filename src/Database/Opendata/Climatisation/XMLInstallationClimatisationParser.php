<?php

namespace App\Database\Opendata\Climatisation;

use App\Database\Opendata\XMLElement;
use App\Domain\Climatisation\{InstallationClimatisation};
use App\Domain\Climatisation\Entity\{Generateur, GenerateurCollection};
use App\Domain\Logement\Logement;

final class XMLInstallationClimatisationParser
{
    public function __construct(private XMLInstallationClimatisationReader $reader)
    {
    }

    public function parse(XMLElement $xml, Logement &$logement): ?InstallationClimatisation
    {
        if (false === $this->reader::apply($xml)) {
            return null;
        }
        $reader = $this->reader->read($xml);
        $entity = new InstallationClimatisation(
            logement: $logement,
            generateur_collection: new GenerateurCollection
        );
        foreach ($reader->generateur_reader() as $generateur_reader) {
            $subentity = new Generateur(
                id: $generateur_reader->id(),
                installation: $entity,
                description: $generateur_reader->description(),
                surface: $generateur_reader->surface_clim(),
                type_generateur: $generateur_reader->type_generateur(),
                annee_installation: $generateur_reader->annee_installation(),
                energie: $generateur_reader->energie(),
                seer: $generateur_reader->seer_saisi(),
            );
            $entity->add_generateur($subentity);
        }
        $logement->set_installation_climatisation($entity);
        return $entity;
    }
}
