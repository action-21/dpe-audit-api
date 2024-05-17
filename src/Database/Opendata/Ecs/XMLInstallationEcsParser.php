<?php

namespace App\Database\Opendata\Ecs;

use App\Database\Opendata\XMLElement;
use App\Domain\Ecs\Entity\{Generateur, GenerateurCollection};
use App\Domain\Ecs\InstallationEcs;
use App\Domain\Logement\Logement;

final class XMLInstallationEcsParser
{
    public function __construct(private XMLInstallationEcsReader $reader)
    {
    }

    public function parse(XMLElement $xml, Logement &$logement): InstallationEcs
    {
        $reader = $this->reader->read($xml);
        $aggregate = new InstallationEcs(
            id: $reader->id(),
            logement: $logement,
            description: $reader->description(),
            pieces_contigues: $reader->pieces_contigues(),
            reseau_distribution_isole: $reader->reseau_distribution_isole(),
            niveaux_desservis: $reader->niveaux_desservis(),
            type_installation: $reader->type_installation(),
            bouclage_reseau: $reader->bouclage_reseau(),
            type_installation_solaire: $reader->type_installation_solaire(),
            fecs: $reader->fecs(),
            generateur_collection: new GenerateurCollection,
        );

        foreach ($reader->generateur_reader() as $generateur_reader) {
            $entity = new Generateur(
                id: $generateur_reader->id(),
                installation: $aggregate,
                identifiant_reseau_chaleur: $generateur_reader->identifiant_reseau_chaleur(),
                description: $generateur_reader->description(),
                position_volume_chauffe: $generateur_reader->position_volume_chauffe(),
                type_generateur: $generateur_reader->type_generateur(),
                usage: $generateur_reader->usage(),
                energie: $generateur_reader->energie(),
                stockage: $generateur_reader->stockage(),
                performance: $generateur_reader->performance(),
                annee_installation: $generateur_reader->annee_installation(),
            );
            $aggregate->add_generateur($entity);
        }
        $logement->set_installation_ecs($aggregate);
        return $aggregate;
    }
}
