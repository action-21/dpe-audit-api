<?php

namespace App\Api\Ventilation\Handler;

use App\Api\Ventilation\Model\Systeme as Payload;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Ventilation\Entity\Systeme;
use App\Domain\Ventilation\Ventilation;

final class CreateSystemeHandler
{
    public function __invoke(Payload $payload, Ventilation $entity): Systeme
    {
        $installation = $entity->installations()->find(Id::from($payload->installation_id));
        $generateur = $payload->generateur_id
            ? $entity->generateurs()->find(Id::from($payload->generateur_id))
            : null;

        return Systeme::create(
            id: Id::from($payload->id),
            ventilation: $entity,
            installation: $installation,
            generateur: $generateur,
            type: $payload->type,
        );
    }
}
