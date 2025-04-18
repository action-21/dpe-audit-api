<?php

namespace App\Api\Ecs;

use App\Api\Ecs\Handler\{CreateGenerateurHandler, CreateInstallationHandler, CreateSystemeHandler};
use App\Api\Ecs\Model\Ecs as Payload;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Factory\GenerateurFactory;

/**
 * @property GenerateurFactory[] $factories
 */
final class CreateEcsHandler
{
    public function __construct(
        private readonly CreateGenerateurHandler $generateur_handler,
        private readonly CreateInstallationHandler $installation_handler,
        private readonly CreateSystemeHandler $systeme_handler,
    ) {}

    public function __invoke(Payload $payload): Ecs
    {
        $entity = Ecs::create();

        foreach ($payload->generateurs as $generateur) {
            $handle = $this->generateur_handler;
            $entity->add_generateur($handle(payload: $generateur, ecs: $entity));
        }
        foreach ($payload->installations as $installation) {
            $handle = $this->installation_handler;
            $entity->add_installation($handle(payload: $installation, ecs: $entity));
        }
        foreach ($payload->systemes as $systeme) {
            $handle = $this->systeme_handler;
            $entity->add_systeme($handle(payload: $systeme, ecs: $entity));
        }
        return $entity;
    }
}
