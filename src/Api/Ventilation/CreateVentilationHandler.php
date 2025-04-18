<?php

namespace App\Api\Ventilation;

use App\Api\Ventilation\Handler\{CreateGenerateurHandler, CreateInstallationHandler, CreateSystemeHandler};
use App\Api\Ventilation\Model\Ventilation as Payload;
use App\Domain\Ventilation\Ventilation;

final class CreateVentilationHandler
{
    public function __construct(
        private readonly CreateGenerateurHandler $generateur_handler,
        private readonly CreateInstallationHandler $installation_handler,
        private CreateSystemeHandler $systeme_handler,
    ) {}

    public function __invoke(Payload $payload): Ventilation
    {
        $entity = Ventilation::create();

        foreach ($payload->generateurs as $generateur) {
            $handle = $this->generateur_handler;
            $entity->add_generateur($handle(payload: $generateur, ventilation: $entity));
        }
        foreach ($payload->installations as $installation) {
            $handle = $this->installation_handler;
            $entity->add_installation($handle(payload: $installation, ventilation: $entity));
        }
        foreach ($payload->systemes as $systeme) {
            $handle = $this->systeme_handler;
            $entity->add_systeme($handle(payload: $systeme, ventilation: $entity));
        }
        return $entity;
    }
}
