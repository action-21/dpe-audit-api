<?php

namespace App\Domain\Lnc;

use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Common\Type\Id;
use App\Domain\Lnc\Entity\{BaieCollection, ParoiCollection};
use App\Domain\Lnc\Enum\TypeLnc;

final class LncFactory
{
    public function build(Id $id, Enveloppe $enveloppe, string $description, TypeLnc $type): Lnc
    {
        return new Lnc(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            type: $type,
            parois: new ParoiCollection,
            baies: new BaieCollection,
        );
    }
}
