<?php

namespace App\Api\Enveloppe\Handler;

use App\Api\Enveloppe\Model\PlancherBas as Payload;
use App\Domain\Common\ValueObject\{Annee, Id};
use App\Domain\Enveloppe\Entity\PlancherBas;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\ValueObject\Isolation;
use App\Domain\Enveloppe\ValueObject\PlancherBas\Position;

final class CreatePlancherBasHandler
{
    public function __invoke(Payload $payload, Enveloppe $entity): PlancherBas
    {
        return PlancherBas::create(
            id: Id::from($payload->id),
            enveloppe: $entity,
            description: $payload->description,
            type_structure: $payload->type_structure,
            inertie: $payload->inertie,
            annee_construction: $payload->annee_construction ? Annee::from($payload->annee_construction) : null,
            annee_renovation: $payload->annee_renovation ? Annee::from($payload->annee_renovation) : null,
            u0: $payload->u0,
            u: $payload->u,
            position: Position::create(
                surface: $payload->position->surface,
                perimetre: $payload->position->perimetre,
                mitoyennete: $payload->position->mitoyennete,
                local_non_chauffe: $payload->position->local_non_chauffe_id
                    ? $entity->locaux_non_chauffes()->find(Id::from($payload->position->local_non_chauffe_id))
                    : null,
            ),
            isolation: Isolation::create(
                etat_isolation: $payload->isolation->etat_isolation,
                type_isolation: $payload->isolation->type_isolation,
                epaisseur_isolation: $payload->isolation->epaisseur_isolation,
                resistance_thermique_isolation: $payload->isolation->resistance_thermique_isolation,
                annee_isolation: $payload->isolation->annee_isolation
                    ? Annee::from($payload->isolation->annee_isolation)
                    : null,
            ),
        );
    }
}
