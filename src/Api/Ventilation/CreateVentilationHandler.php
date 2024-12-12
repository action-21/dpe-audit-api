<?php

namespace App\Api\Ventilation;

use App\Api\Ventilation\Payload\VentilationPayload;
use App\Domain\Audit\Audit;
use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\Ventilation;
use App\Domain\Ventilation\Entity\{Generateur, Installation, Systeme};

final class CreateVentilationHandler
{
    public function __invoke(VentilationPayload $payload, Audit $audit,): Ventilation
    {
        $ventilation = Ventilation::create(audit: $audit);

        foreach ($payload->generateurs as $generateur_payload) {
            $ventilation->add_generateur(Generateur::create(
                id: Id::from($generateur_payload->id),
                ventilation: $ventilation,
                description: $generateur_payload->description,
                signaletique: $generateur_payload->signaletique->to(),
                generateur_collectif: $generateur_payload->generateur_collectif,
                annee_installation: $generateur_payload->annee_installation,
            ));
        }
        foreach ($payload->installations as $installation_payload) {
            $installation = Installation::create(
                id: Id::from($installation_payload->id),
                ventilation: $ventilation,
                surface: $installation_payload->surface,
            );

            foreach ($installation_payload->systemes as $systeme_payload) {
                $generateur = $systeme_payload->generateur_id()
                    ? $ventilation->generateurs()->find(Id::from($systeme_payload->generateur_id()))
                    : null;

                if ($systeme_payload->generateur_id() && null === $generateur) {
                    throw new \InvalidArgumentException('Generateur not found');
                }

                $installation->add_systeme(Systeme::create(
                    id: Id::from($systeme_payload->id),
                    installation: $installation,
                    type_ventilation: $systeme_payload->type_ventilation,
                    generateur: $generateur,
                ));
            }
        }
        return $ventilation;
    }
}
