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
        foreach ($root->read_chauffage()->read_generateurs() as $generateur_reader) {
            if ($chauffage->generateurs()->find(id: $generateur_reader->id()))
                continue;

            $generateur = new Generateur(
                id: $generateur_reader->id(),
                chauffage: $chauffage,
                generateur_mixte_id: $generateur_reader->match_generateur_mixte(),
                reseau_chaleur_id: $generateur_reader->reseau_chaleur_id(),
                description: $generateur_reader->description(),
                signaletique: $generateur_reader->signaletique(),
                annee_installation: $generateur_reader->annee_installation(),
                position_volume_chauffe: $generateur_reader->position_volume_chauffe(),
                generateur_collectif: $generateur_reader->generateur_collectif(),
            );
            $chauffage->add_generateur($generateur);
        }
    }

    private function set_emetteurs(XMLElement $root, Chauffage $chauffage): void
    {
        foreach ($root->read_chauffage()->read_emetteurs() as $emetteur_reader) {
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

    private function set_installations(XMLElement $root, Chauffage $chauffage): void
    {
        foreach ($root->read_chauffage()->read_installations() as $installation_reader) {
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
                if ($installation_reader->has_appoint_electrique_sdb() && $generateur_reader->is_appoint_electrique_sdb())
                    continue;
                if (null === $generateur = $chauffage->generateurs()->find(id: $generateur_reader->id()))
                    throw new \RuntimeException("Generateur {$generateur_reader->id()} non trouvé");

                $systeme = new Systeme(
                    id: $generateur_reader->id(),
                    installation: $installation,
                    generateur: $generateur,
                    reseau: $generateur_reader->reseau(),
                    emetteurs: new EmetteurCollection(),
                );

                foreach ($generateur_reader->read_emetteurs() as $emetteur_reader) {
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
        foreach ($root->read_chauffage()->read_installations() as $installation_reader) {
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
                if (false === $generateur_reader->is_appoint_electrique_sdb())
                    continue;
                if (null === $generateur = $chauffage->generateurs()->find(id: $generateur_reader->id()))
                    throw new \RuntimeException("Generateur {$generateur_reader->id()} non trouvé");

                $systeme = new Systeme(
                    id: $generateur_reader->id(),
                    installation: $installation,
                    generateur: $generateur,
                    reseau: $generateur_reader->reseau(),
                    emetteurs: new EmetteurCollection(),
                );
                $installation->add_systeme($systeme);
            }
            $chauffage->add_installation($installation);
        }
    }
}
