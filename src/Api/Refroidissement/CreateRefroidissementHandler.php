<?php

namespace App\Api\Refroidissement;

use App\Api\Refroidissement\Handler\CreateGenerateurHandler;
use App\Api\Refroidissement\Handler\CreateInstallationHandler;
use App\Api\Refroidissement\Handler\CreateSystemeHandler;
use App\Api\Refroidissement\Model\Refroidissement as Payload;
use App\Domain\Refroidissement\Refroidissement;

final class CreateRefroidissementHandler
{
    public function __construct(
        private readonly CreateGenerateurHandler $generateur_handler,
        private readonly CreateInstallationHandler $installation_handler,
        private readonly CreateSystemeHandler $systeme_handler,
    ) {}
    public function __invoke(Payload $payload): Refroidissement
    {
        $entity = Refroidissement::create();

        foreach ($payload->generateurs as $generateur) {
            $entity->add_generateur(
                $this->generateur_handler->__invoke(payload: $generateur, entity: $entity)
            );
        }
        foreach ($payload->installations as $installation) {
            $entity->add_installation(
                $this->installation_handler->__invoke(payload: $installation, entity: $entity)
            );
        }
        foreach ($payload->systemes as $systeme) {
            $entity->add_systeme(
                $this->systeme_handler->__invoke(payload: $systeme, entity: $entity)
            );
        }
        return $entity;
    }
}
