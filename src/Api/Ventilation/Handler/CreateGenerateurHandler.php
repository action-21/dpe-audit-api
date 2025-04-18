<?php

namespace App\Api\Ventilation\Handler;

use App\Api\Ventilation\Model\Generateur as Payload;
use App\Domain\Common\ValueObject\{Annee, Id};
use App\Domain\Ventilation\Entity\Generateur;
use App\Domain\Ventilation\Ventilation;

final class CreateGenerateurHandler
{
    public function __invoke(Payload $payload, Ventilation $entity): Generateur
    {
        return Generateur::create(
            id: Id::from($payload->id),
            ventilation: $entity,
            description: $payload->description,
            type: $payload->type,
            presence_echangeur_thermique: $payload->presence_echangeur_thermique,
            generateur_collectif: $payload->generateur_collectif,
            type_vmc: $payload->type_vmc,
            annee_installation: $payload->annee_installation
                ? Annee::from($payload->annee_installation)
                : null,
        );
    }
}
