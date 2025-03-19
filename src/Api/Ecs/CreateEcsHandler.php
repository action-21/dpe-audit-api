<?php

namespace App\Api\Ecs;

use App\Api\Ecs\Payload\EcsPayload;
use App\Domain\Audit\Audit;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Entity\{Generateur, Installation, Systeme};

final class CreateEcsHandler
{
    public function __invoke(EcsPayload $payload, Audit $audit): Ecs
    {
        $ecs = Ecs::create(audit: $audit);

        foreach ($payload->generateurs as $generateur_payload) {
            $ecs->add_generateur(Generateur::create(
                id: Id::from($generateur_payload->id),
                ecs: $ecs,
                description: $generateur_payload->description,
                generateur_mixte_id: $generateur_payload->generateur_mixte_id ? Id::from($generateur_payload->generateur_mixte_id) : null,
                reseau_chaleur_id: $generateur_payload->reseau_chaleur_id ? Id::from($generateur_payload->reseau_chaleur_id) : null,
                annee_installation: $generateur_payload->annee_installation,
                position_volume_chauffe: $generateur_payload->position_volume_chauffe,
                generateur_collectif: $generateur_payload->generateur_collectif,
                signaletique: $generateur_payload->signaletique->to(),
            ));
        }

        foreach ($payload->installations as $installation_payload) {
            $installation = Installation::create(
                id: Id::from($installation_payload->id),
                ecs: $ecs,
                description: $installation_payload->description,
                surface: $installation_payload->surface,
                solaire: $installation_payload->solaire?->to(),
            );

            foreach ($installation_payload->systemes as $systeme_payload) {
                if (null === $generateur = $ecs->generateurs()->get(Id::from($systeme_payload->generateur_id))) {
                    throw new \InvalidArgumentException('Generateur not found');
                }
                $installation->add_systeme(Systeme::create(
                    id: Id::from($systeme_payload->id),
                    installation: $installation,
                    generateur: $generateur,
                    reseau: $systeme_payload->reseau->to(),
                    stockage: $systeme_payload->stockage?->to(),
                ));
            }
        }
        return $ecs;
    }
}
