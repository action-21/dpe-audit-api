<?php

namespace App\Api\Enveloppe\Handler;

use App\Api\Enveloppe\Model\Porte as Payload;
use App\Domain\Common\ValueObject\{Annee, Id, Orientation, Pourcentage};
use App\Domain\Enveloppe\Entity\Porte;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\ValueObject\Porte\{Menuiserie, Position, Vitrage};

final class CreatePorteHandler
{
    public function __invoke(Payload $payload, Enveloppe $entity): Porte
    {
        return Porte::create(
            id: Id::from($payload->id),
            enveloppe: $entity,
            description: $payload->description,
            type_pose: $payload->type_pose,
            isolation: $payload->isolation,
            materiau: $payload->materiau,
            presence_sas: $payload->presence_sas,
            annee_installation: $payload->annee_installation
                ? Annee::from($payload->annee_installation)
                : null,
            u: $payload->u,
            position: Position::create(
                surface: $payload->position->surface,
                mitoyennete: $payload->position->mitoyennete,
                orientation: $payload->position->orientation
                    ? Orientation::from($payload->position->orientation)
                    : null,
                local_non_chauffe: $payload->position->local_non_chauffe_id
                    ? $entity->locaux_non_chauffes()->find(Id::from($payload->position->local_non_chauffe_id))
                    : null,
                paroi: $payload->position->paroi_id
                    ? $entity->paroi(Id::from($payload->position->paroi_id))
                    : null,
            ),
            vitrage: Vitrage::create(
                taux_vitrage: Pourcentage::from($payload->vitrage->taux_vitrage),
                type_vitrage: $payload->vitrage->type_vitrage,
            ),
            menuiserie: Menuiserie::create(
                presence_joint: $payload->menuiserie->presence_joint,
                presence_retour_isolation: $payload->menuiserie->presence_retour_isolation,
                largeur_dormant: $payload->menuiserie->largeur_dormant,
            )
        );
    }
}
