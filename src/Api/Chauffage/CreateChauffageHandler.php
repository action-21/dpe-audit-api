<?php

namespace App\Api\Chauffage;

use App\Api\Chauffage\Payload\ChauffagePayload;
use App\Domain\Audit\Audit;
use App\Domain\Common\Type\Id;
use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Entity\{Emetteur, Generateur, Installation, Systeme};

final class CreateChauffageHandler
{
    public function __invoke(ChauffagePayload $payload, Audit $audit): Chauffage
    {
        $chauffage = Chauffage::create(audit: $audit);

        foreach ($payload->generateurs as $generateur_payload) {
            $chauffage->add_generateur(Generateur::create(
                id: Id::from($generateur_payload->id),
                chauffage: $chauffage,
                description: $generateur_payload->description,
                generateur_mixte_id: $generateur_payload->generateur_mixte_id ? Id::from($generateur_payload->generateur_mixte_id) : null,
                reseau_chaleur_id: $generateur_payload->reseau_chaleur_id ? Id::from($generateur_payload->reseau_chaleur_id) : null,
                annee_installation: $generateur_payload->annee_installation,
                position_volume_chauffe: $generateur_payload->position_volume_chauffe,
                generateur_collectif: $generateur_payload->generateur_collectif,
                signaletique: $generateur_payload->signaletique->to(),
            ));
        }

        foreach ($payload->emetteurs as $emetteur_payload) {
            $chauffage->add_emetteur(Emetteur::create(
                id: Id::from($emetteur_payload->id),
                chauffage: $chauffage,
                description: $emetteur_payload->description,
                type: $emetteur_payload->type,
                temperature_distribution: $emetteur_payload->temperature_distribution,
                presence_robinet_thermostatique: $emetteur_payload->presence_robinet_thermostatique,
                annee_installation: $emetteur_payload->annee_installation,
            ));
        }

        foreach ($payload->installations as $installation_payload) {
            $installation = Installation::create(
                id: Id::from($installation_payload->id),
                chauffage: $chauffage,
                description: $installation_payload->description,
                surface: $installation_payload->surface,
                comptage_individuel: $installation_payload->comptage_individuel,
                solaire: $installation_payload->solaire?->to(),
                regulation_centrale: $installation_payload->regulation_centrale?->to(),
                regulation_terminale: $installation_payload->regulation_terminale?->to(),
            );

            foreach ($installation_payload->systemes as $systeme_payload) {
                if (null === $generateur = $chauffage->generateurs()->get(Id::from($systeme_payload->generateur_id))) {
                    throw new \InvalidArgumentException('Generateur not found');
                }
                $systeme = Systeme::create(
                    id: Id::from($systeme_payload->id),
                    installation: $installation,
                    generateur: $generateur,
                    reseau: $systeme_payload->reseau->to(),
                );
                foreach ($systeme_payload->emetteurs as $id) {
                    if (null === $emetteur = $chauffage->emetteurs()->get(Id::from($id))) {
                        throw new \InvalidArgumentException('Emetteur not found');
                    }
                    $systeme->reference_emetteur($emetteur);
                }
                $installation->add_systeme($systeme);
            }
        }
        return $chauffage;
    }
}
