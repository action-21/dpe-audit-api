<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\XMLElement;
use App\Domain\Logement\Logement;
use App\Domain\Ventilation\InstallationVentilation;
use App\Domain\Ventilation\Entity\{Ventilation, VentilationCollection};

final class XMLInstallationVentilationParser
{
    public function __construct(private XMLInstallationVentilationReader $reader)
    {
    }

    public function parse(XMLElement $xml, Logement &$logement): InstallationVentilation
    {
        $reader = $this->reader->read($xml);
        $entity = new InstallationVentilation(
            logement: $logement,
            ventilation_collection: new VentilationCollection
        );
        foreach ($reader->ventilation_reader() as $ventilation_reader) {
            $subentity = new Ventilation(
                id: $ventilation_reader->id(),
                installation: $entity,
                description: $ventilation_reader->description(),
                surface: $ventilation_reader->surface_ventilee(),
                type_ventilation: $ventilation_reader->type_ventilation(),
                type_installation: $ventilation_reader->type_installation(),
                annee_installation: $ventilation_reader->annee_installation(),
            );
            $entity->add_ventilation($subentity);
        }
        $logement->set_installation_ventilation($entity);
        return $entity;
    }
}
