<?php

namespace App\Domain\Lnc\Factory;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Lnc\Entity\ParoiOpaque;
use App\Domain\Lnc\Enum\{EtatIsolation};
use App\Domain\Lnc\Lnc;
use App\Domain\Lnc\ValueObject\PositionParoi;

final class ParoiFactory
{
    public function build(
        Id $id,
        Lnc $local_non_chauffe,
        string $description,
        ?EtatIsolation $isolation,
        PositionParoi $position,
    ): ParoiOpaque {
        $entity = new ParoiOpaque(
            id: $id,
            local_non_chauffe: $local_non_chauffe,
            description: $description,
            isolation: $isolation,
            position: $position,
        );

        $entity->controle();
        return $entity;
    }
}
