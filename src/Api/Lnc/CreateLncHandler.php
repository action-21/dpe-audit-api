<?php

namespace App\Api\Lnc;

use App\Api\Lnc\Payload\LncPayload;
use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Lnc\Entity\{Baie, Paroi};
use App\Domain\Lnc\Lnc;

final class CreateLncHandler
{
    public function __invoke(LncPayload $payload, Enveloppe $enveloppe,): Lnc
    {
        $lnc = Lnc::create(
            id: Id::from($payload->id),
            enveloppe: $enveloppe,
            description: $payload->description,
            type: $payload->type,
        );

        foreach ($payload->parois as $paroi_payload) {
            $lnc->add_paroi(Paroi::create(
                id: Id::from($paroi_payload->id),
                local_non_chauffe: $lnc,
                description: $paroi_payload->description,
                position: $paroi_payload->position->to(),
                surface: $paroi_payload->surface,
                etat_isolation: $paroi_payload->etat_isolation,
            ));
        }
        foreach ($payload->baies as $baie_payload) {
            $lnc->add_baie(Baie::create(
                id: Id::from($baie_payload->id),
                local_non_chauffe: $lnc,
                description: $baie_payload->description,
                position: $baie_payload->position->to(),
                type: $baie_payload->type,
                surface: $baie_payload->surface,
                inclinaison: $baie_payload->inclinaison,
                menuiserie: $baie_payload->menuiserie?->to(),
            ));
        }
        return $lnc;
    }
}
