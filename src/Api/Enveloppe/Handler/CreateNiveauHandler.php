<?php

namespace App\Api\Enveloppe\Handler;

use App\Api\Enveloppe\Model\Niveau as Payload;
use App\Domain\Enveloppe\Entity\Niveau;
use App\Domain\Enveloppe\Enveloppe;

final class CreateNiveauHandler
{
    public function __invoke(Payload $payload, Enveloppe $entity): Niveau
    {
        return Niveau::create(
            enveloppe: $entity,
            surface: $payload->surface,
            inertie_paroi_verticale: $payload->inertie_paroi_verticale,
            inertie_plancher_haut: $payload->inertie_plancher_haut,
            inertie_plancher_bas: $payload->inertie_plancher_bas,
        );
    }
}
