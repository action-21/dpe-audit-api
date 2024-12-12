<?php

namespace App\Api\Visite;

use App\Api\Visite\Payload\VisitePayload;
use App\Domain\Audit\Audit;
use App\Domain\Common\Type\Id;
use App\Domain\Visite\Visite;
use App\Domain\Visite\Entity\Logement;

final class CreateVisiteHandler
{
    public function __invoke(VisitePayload $payload, Audit $audit): Visite
    {
        $visite = Visite::create($audit);

        foreach ($payload->logements as $logement_payload) {
            $visite->add_logement(Logement::create(
                id: Id::from($logement_payload->id),
                visite: $visite,
                description: $logement_payload->description,
                typologie: $logement_payload->typologie,
                surface_habitable: $logement_payload->surface_habitable,
            ));
        }
        return $visite;
    }
}
