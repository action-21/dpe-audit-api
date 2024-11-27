<?php

namespace App\Database\Opendata\Chauffage;

use App\Database\Opendata\Audit\XMLAuditTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Chauffage\{Chauffage, ChauffageFactory};
use App\Domain\Chauffage\Entity\{Emetteur, EmetteurCollection, Generateur, Installation, Systeme, SystemeCollection};

final class XMLChauffageTransformer
{
    public function __construct(
        private ChauffageFactory $factory,
        private XMLAuditTransformer $audit_transformer,
        private XMLInstallationReader $installation_reader,
    ) {}

    public function transform(XMLElement $root): Chauffage
    {
        $audit = $this->audit_transformer->transform($root);
        $chauffage = $this->factory->build($audit);

        $this->set_generateurs($root, $chauffage);
        $this->set_emetteurs($root, $chauffage);
        $this->set_installations($root, $chauffage);
        $this->set_installations_sdb($root, $chauffage);
        return $chauffage;
    }

    private function set_generateurs(XMLElement $root, Chauffage $chauffage): void
    {
        foreach ($this->installation_reader->read($root->installation_chauffage_collection()) as $installation_reader) {
            $installation_collective = $installation_reader->installation_collective();

            foreach ($installation_reader->read_generateurs() as $generateur_reader) {
                if (false === $generateur_reader->apply())
                    continue;
                if ($chauffage->generateurs()->find(id: $generateur_reader->id()))
                    continue;

                $generateur = new Generateur(
                    id: $generateur_reader->id(),
                    chauffage: $chauffage,
                    generateur_mixte_id: $generateur_reader->generateur_mixte_id(),
                    reseau_chaleur_id: $generateur_reader->reseau_chaleur_id(),
                    description: $generateur_reader->description(),
                    type: $generateur_reader->type_generateur(),
                    energie: $generateur_reader->energie_generateur(),
                    position_volume_chauffe: $generateur_reader->position_volume_chauffe(),
                    generateur_collectif: $installation_collective && false === $generateur_reader->generateur_appoint(),
                    signaletique: $generateur_reader->signaletique(),
                    annee_installation: $generateur_reader->annee_installation(),
                    type_partie_chaudiere: $generateur_reader->type_partie_chaudiere(),
                    energie_partie_chaudiere: $generateur_reader->energie_partie_chaudiere(),
                );
                $generateur->determine_categorie();
                $chauffage->add_generateur($generateur);
            }
        }
    }

    private function set_emetteurs(XMLElement $root, Chauffage $chauffage): void
    {
        foreach ($this->installation_reader->read($root->installation_chauffage_collection()) as $installation_reader) {
            foreach ($installation_reader->read_emetteurs() as $emetteur_reader) {
                if (false === $emetteur_reader->apply())
                    continue;
                if ($chauffage->emetteurs()->find(id: $emetteur_reader->id()))
                    continue;

                $emetteur = new Emetteur(
                    id: $emetteur_reader->id(),
                    chauffage: $chauffage,
                    description: $emetteur_reader->description(),
                    type: $emetteur_reader->type_emetteur(),
                    temperature_distribution: $emetteur_reader->temperature_distribution(),
                    presence_robinet_thermostatique: $emetteur_reader->presence_robinet_thermostatique(),
                    annee_installation: $emetteur_reader->annee_installation(),
                );
                $chauffage->add_emetteur($emetteur);
            }
        }
    }

    private function set_installations(XMLElement $root, Chauffage $chauffage): void
    {
        foreach ($this->installation_reader->read($root->installation_chauffage_collection()) as $installation_reader) {
            $installation = new Installation(
                id: $installation_reader->id(),
                chauffage: $chauffage,
                description: $installation_reader->description(),
                surface: $installation_reader->surface(),
                comptage_individuel: $installation_reader->comptage_individuel() ?? true,
                solaire: $installation_reader->solaire(),
                regulation_centrale: $installation_reader->regulation_centrale(),
                regulation_terminale: $installation_reader->regulation_terminale(),
                systemes: new SystemeCollection(),
            );

            foreach ($installation_reader->read_generateurs() as $generateur_reader) {
                if (false === $generateur_reader->apply())
                    continue;
                if ($installation_reader->has_appoint_electrique_sdb() && $generateur_reader->is_appoint_electrique_sdb())
                    continue;
                if (null === $generateur = $chauffage->generateurs()->find(id: $generateur_reader->id()))
                    throw new \RuntimeException("Generateur {$generateur_reader->id()} non trouvé");

                $systeme = new Systeme(
                    id: $generateur_reader->id(),
                    installation: $installation,
                    generateur: $generateur,
                    type_distribution: $installation_reader->type_distribution(
                        enum_lien_generateur_emetteur_id: $generateur_reader->enum_lien_generateur_emetteur_id(),
                    ),
                    position_volume_chauffe: $generateur_reader->position_volume_chauffe(),
                    reseau: $installation_reader->reseau(),
                    emetteurs: new EmetteurCollection(),
                );

                foreach ($installation_reader->read_emetteurs() as $emetteur_reader) {
                    if (false === $emetteur_reader->apply())
                        continue;
                    if (null === $emetteur = $chauffage->emetteurs()->find(id: $emetteur_reader->id()))
                        throw new \RuntimeException("Emetteur {$emetteur_reader->id()} non trouvé");

                    $systeme->reference_emetteur($emetteur);
                }
                $installation->add_systeme($systeme);
            }

            $chauffage->add_installation($installation);
        }
    }

    private function set_installations_sdb(XMLElement $root, Chauffage $chauffage): void
    {
        foreach ($this->installation_reader->read($root->installation_chauffage_collection()) as $installation_reader) {
            if (false === $installation_reader->has_appoint_electrique_sdb())
                continue;

            $installation = new Installation(
                id: $installation_reader->id(),
                chauffage: $chauffage,
                description: $installation_reader->description(),
                surface: $installation_reader->surface(),
                comptage_individuel: $installation_reader->comptage_individuel() ?? true,
                solaire: $installation_reader->solaire(),
                regulation_centrale: $installation_reader->regulation_centrale(),
                regulation_terminale: $installation_reader->regulation_terminale(),
                systemes: new SystemeCollection(),
            );

            foreach ($installation_reader->read_generateurs() as $generateur_reader) {
                if (false === $generateur_reader->apply())
                    continue;
                if (false === $generateur_reader->is_appoint_electrique_sdb())
                    continue;
                if (null === $generateur = $chauffage->generateurs()->find(id: $generateur_reader->id()))
                    throw new \RuntimeException("Generateur {$generateur_reader->id()} non trouvé");

                $systeme = new Systeme(
                    id: $generateur_reader->id(),
                    installation: $installation,
                    generateur: $generateur,
                    type_distribution: $installation_reader->type_distribution(
                        enum_lien_generateur_emetteur_id: $generateur_reader->enum_lien_generateur_emetteur_id(),
                    ),
                    position_volume_chauffe: $generateur_reader->position_volume_chauffe(),
                    reseau: $installation_reader->reseau(),
                    emetteurs: new EmetteurCollection(),
                );
                $installation->add_systeme($systeme);
            }
            $chauffage->add_installation($installation);
        }
    }
}
