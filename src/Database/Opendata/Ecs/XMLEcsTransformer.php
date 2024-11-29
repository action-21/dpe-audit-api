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

    /**
     * TODO: merge pac hybride
     */
    private function set_generateurs(XMLElement $root, Ecs $ecs): void
    {
        foreach ($root->read_installations_ecs() as $installation_reader) {
            $installation_collective = $installation_reader->installation_collective();

            foreach ($installation_reader->read_generateurs() as $generateur_reader) {
                if (false === $generateur_reader->apply())
                    continue;
                if ($ecs->generateurs()->find(id: $generateur_reader->id()))
                    continue;
                if ($generateur_reader->generateur_mixte_id() && false === $generateur_reader->generateur_mixte_exists())
                    throw new \RuntimeException("Generateur mixte {$generateur_reader->generateur_mixte_id()} non accessible");

                $generateur = new Generateur(
                    id: $generateur_reader->id(),
                    ecs: $ecs,
                    description: $generateur_reader->description(),
                    type: $generateur_reader->type(),
                    energie: $generateur_reader->energie(),
                    volume_stockage: $generateur_reader->volume_stockage(),
                    position_volume_chauffe: $generateur_reader->position_volume_chauffe(),
                    generateur_collectif: $installation_collective,
                    signaletique: $generateur_reader->signaletique(),
                    annee_installation: $generateur_reader->annee_installation(),
                    generateur_mixte_id: $generateur_reader->generateur_mixte_id(),
                    reseau_chaleur_id: $generateur_reader->reseau_chaleur_id(),
                );
                $generateur->determine_categorie();
                $ecs->add_generateur($generateur);
            }
        }
    }

    /**
     * IMPORTANT : La puissance par défaut des générateurs ne peut être évaluée depuis les données de l'open data
     * (type de chaudière manquant). On considère donc la donnée intermédiaire pn comme étant la puissance nominale saisie.
     */
    private function set_installations(XMLElement $root, Ecs $ecs): void
    {
        foreach ($root->read_installations_ecs() as $installation_reader) {
            $installation = new Installation(
                id: $installation_reader->id(),
                ecs: $ecs,
                description: $installation_reader->description(),
                surface: $installation_reader->surface_habitable(),
                solaire: $installation_reader->solaire(),
                systemes: new SystemeCollection(),
            );

            foreach ($installation_reader->read_generateurs() as $generateur_reader) {
                if (false === $generateur_reader->apply())
                    continue;
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
