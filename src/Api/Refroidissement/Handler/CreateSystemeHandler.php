<?php

namespace App\Api\Refroidissement\Handler;

use App\Api\Refroidissement\Model\Systeme as Payload;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\Entity\Systeme;
use App\Domain\Refroidissement\Refroidissement;

final class CreateSystemeHandler
{
    public function __invoke(Payload $payload, Refroidissement $entity): Systeme
    {
        $installation = $entity->installations()->find(Id::from($payload->installation_id));
        $generateur = $payload->generateur_id
            ? $entity->generateurs()->find(Id::from($payload->generateur_id))
            : null;

        return Systeme::create(
            id: Id::from($payload->id),
            refroidissement: $entity,
            installation: $installation,
            generateur: $generateur,
        );
    }
}
