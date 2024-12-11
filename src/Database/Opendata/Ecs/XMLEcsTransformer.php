<?php

namespace App\Database\Opendata\Ecs;

use App\Database\Opendata\Audit\XMLAuditTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Ecs\{Ecs, EcsFactory};
use App\Domain\Ecs\Entity\{Installation, Generateur, Systeme, SystemeCollection};

final class XMLEcsTransformer
{
    public function __construct(
        private EcsFactory $factory,
        private XMLAuditTransformer $audit_transformer,
    ) {}

    public function transform(XMLElement $root): Ecs
    {
        $audit = $this->audit_transformer->transform($root);
        $ecs = $this->factory->build($audit);

        $this->set_generateurs($root, $ecs);
        $this->set_installations($root, $ecs);
        return $ecs;
    }

    private function set_generateurs(XMLElement $root, Ecs $ecs): void
    {
        foreach ($root->read_ecs()->read_generateurs() as $generateur_reader) {
            if ($ecs->generateurs()->find(id: $generateur_reader->id()))
                continue;

            $generateur = new Generateur(
                id: $generateur_reader->id(),
                ecs: $ecs,
                description: $generateur_reader->description(),
                signaletique: $generateur_reader->signaletique(),
                annee_installation: $generateur_reader->annee_installation(),
                generateur_mixte_id: $generateur_reader->match_generateur_mixte(),
                reseau_chaleur_id: $generateur_reader->reseau_chaleur_id(),
                position_volume_chauffe: $generateur_reader->position_volume_chauffe(),
                generateur_collectif: $generateur_reader->generateur_collectif(),
            );
            $ecs->add_generateur($generateur);
        }
    }

    private function set_installations(XMLElement $root, Ecs $ecs): void
    {
        foreach ($root->read_ecs()->read_installations() as $installation_reader) {
            $installation = new Installation(
                id: $installation_reader->id(),
                ecs: $ecs,
                description: $installation_reader->description(),
                surface: $installation_reader->surface_habitable(),
                solaire: $installation_reader->solaire(),
                systemes: new SystemeCollection(),
            );

            foreach ($installation_reader->read_generateurs() as $generateur_reader) {
                if (null === $generateur = $ecs->generateurs()->find(id: $generateur_reader->id()))
                    throw new \RuntimeException("Generateur {$generateur_reader->id()} non trouvé");

                $systeme = new Systeme(
                    id: $generateur_reader->id(),
                    installation: $installation,
                    generateur: $generateur,
                    reseau: $installation_reader->reseau(),
                    stockage: $generateur_reader->stockage(),
                );

                $installation->add_systeme($systeme);
            }
            $ecs->add_installation($installation);
        }
    }
}
