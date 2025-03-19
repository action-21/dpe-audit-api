<?php

namespace App\Domain\Lnc;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Lnc\Entity\{BaieCollection, ParoiOpaqueCollection};
use App\Domain\Lnc\Enum\TypeLnc;

final class LncFactory
{
    public function build(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        TypeLnc $type,
    ): Lnc {
        $entity = new Lnc(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            type: $type,
            parois: new ParoiOpaqueCollection,
            baies: new BaieCollection,
        );

        $entity->controle();
        return $entity;
    }
}
