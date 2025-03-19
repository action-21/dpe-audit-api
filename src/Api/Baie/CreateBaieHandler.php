<?php

namespace App\Api\Baie;

use App\Api\Baie\Payload\BaiePayload;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Baie\Baie;
use App\Domain\Baie\Entity\{MasqueLointain, MasqueProche};

final class CreateBaieHandler
{
    public function __invoke(BaiePayload $payload, Enveloppe $enveloppe,): Baie
    {
        $baie = Baie::create(
            id: Id::from($payload->id),
            enveloppe: $enveloppe,
            description: $payload->description,
            position: $payload->position->to(),
            caracteristique: $payload->caracteristique->to(),
            double_fenetre: $payload->double_fenetre->to(),
        );

        foreach ($payload->masques_proches as $masque_payload) {
            $baie->add_masque_proche(MasqueProche::create(
                baie: $baie,
                description: $masque_payload->description,
                type_masque: $masque_payload->type,
                avancee: $masque_payload->avancee,
            ));
        }

        foreach ($payload->masques_lointains as $masque_payload) {
            $baie->add_masque_lointain(MasqueLointain::create(
                baie: $baie,
                description: $masque_payload->description,
                type_masque: $masque_payload->type,
                hauteur: $masque_payload->hauteur,
                orientation: $masque_payload->orientation,
            ));
        }

        return $baie;
    }
}
