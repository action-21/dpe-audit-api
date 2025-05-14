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
        private readonly CreateSystemeHandler $systeme_handler,
    ) {}

    public function __invoke(Payload $payload): Ventilation
    {
        $entity = Ventilation::create();

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
