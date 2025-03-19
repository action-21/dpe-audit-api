<?php

namespace App\Domain\Porte;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Porte\Enum\{EtatIsolation, Materiau, TypePose};
use App\Domain\Porte\ValueObject\{Menuiserie, Position, Vitrage};

final class PorteFactory
{
    public function build(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        TypePose $type_pose,
        ?EtatIsolation $isolation,
        ?Materiau $materiau,
        bool $presence_sas,
        ?int $annee_installation,
        ?float $u,
        Position $position,
        Menuiserie $menuiserie,
        Vitrage $vitrage,
    ): Porte {
        $entity = new Porte(
            id: $id,
            description: $description,
            type_pose: $type_pose,
            isolation: $isolation,
            materiau: $materiau,
            presence_sas: $presence_sas,
            annee_installation: $annee_installation,
            u: $u,
            position: $position,
            menuiserie: $menuiserie,
            vitrage: $vitrage,
            enveloppe: $enveloppe,
        );
        $entity->controle();
        return $entity;
    }
}
