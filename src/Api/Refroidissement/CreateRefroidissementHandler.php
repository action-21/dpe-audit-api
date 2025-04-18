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
        private CreateGenerateurHandler $generateur_handler,
        private CreateInstallationHandler $installation_handler,
        private CreateSystemeHandler $systeme_handler,
    ) {}
    public function __invoke(Payload $payload): Refroidissement
    {
        $entity = Refroidissement::create();

        foreach ($payload->generateurs as $generateur) {
            $handle = $this->generateur_handler;
            $entity->add_generateur($handle(payload: $generateur, refroidissement: $entity));
        }
        foreach ($payload->installations as $installation) {
            $handle = $this->installation_handler;
            $entity->add_installation($handle(payload: $installation, refroidissement: $entity));
        }
        foreach ($payload->systemes as $systeme) {
            $handle = $this->systeme_handler;
            $entity->add_systeme($handle(payload: $systeme, refroidissement: $entity));
        }
        return $entity;
    }
}
