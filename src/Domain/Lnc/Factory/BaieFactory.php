<?php

namespace App\Domain\Lnc\Factory;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Lnc\Entity\Baie;
use App\Domain\Lnc\Enum\{Materiau, TypeBaie, TypeVitrage};
use App\Domain\Lnc\Lnc;
use App\Domain\Lnc\ValueObject\PositionBaie;

final class BaieFactory
{
    public function build(
        Id $id,
        Lnc $local_non_chauffe,
        string $description,
        TypeBaie $type,
        ?Materiau $materiau,
        ?TypeVitrage $type_vitrage,
        ?bool $presence_rupteur_pont_thermique,
        PositionBaie $position,
    ): Baie {
        $entity = new Baie(
            id: $id,
            local_non_chauffe: $local_non_chauffe,
            description: $description,
            type: $type,
            materiau: $materiau,
            type_vitrage: $type_vitrage,
            presence_rupteur_pont_thermique: $presence_rupteur_pont_thermique,
            position: $position,
        );

        $entity->controle();
        return $entity;
    }
}
