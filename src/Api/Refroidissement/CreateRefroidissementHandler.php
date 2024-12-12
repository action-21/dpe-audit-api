<?php

namespace App\Api\Refroidissement;

use App\Api\Refroidissement\Payload\RefroidissementPayload;
use App\Domain\Audit\Audit;
use App\Domain\Common\Type\Id;
use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Refroidissement\Entity\{Generateur, Installation, Systeme};

final class CreateRefroidissementHandler
{
    public function __invoke(RefroidissementPayload $payload, Audit $audit,): Refroidissement
    {
        $refroidissement = Refroidissement::create(audit: $audit);

        foreach ($payload->generateurs as $generateur_payload) {
            $refroidissement->add_generateur(Generateur::create(
                id: Id::from($generateur_payload->id),
                refroidissement: $refroidissement,
                description: $generateur_payload->description,
                signaletique: $generateur_payload->signaletique->to(),
                annee_installation: $generateur_payload->annee_installation,
                reseau_froid_id: $generateur_payload->reseau_froid_id ? Id::from($generateur_payload->reseau_froid_id) : null,
            ));
        }
        foreach ($payload->installations as $installation_payload) {
            $installation = Installation::create(
                id: Id::from($installation_payload->id),
                refroidissement: $refroidissement,
                description: $installation_payload->description,
                surface: $installation_payload->surface,
            );

            foreach ($installation_payload->systemes as $systeme_payload) {
                if (null === $generateur = $refroidissement->generateurs()->find(Id::from($systeme_payload->generateur_id))) {
                    throw new \InvalidArgumentException('Generateur not found');
                }

                $installation->add_systeme(Systeme::create(
                    id: Id::from($systeme_payload->id),
                    installation: $installation,
                    generateur: $generateur,
                ));
            }
        }
        return $refroidissement;
    }
}
