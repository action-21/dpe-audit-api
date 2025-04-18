<?php

namespace App\Api\Chauffage;

use App\Api\Chauffage\Handler\{CreateEmetteurHandler, CreateGenerateurHandler, CreateInstallationHandler, CreateSystemeHandler};
use App\Api\Chauffage\Model\Chauffage as Payload;
use App\Domain\Chauffage\Chauffage;

final class CreateChauffageHandler
{
    public function __construct(
        private readonly CreateGenerateurHandler $generateur_handler,
        private readonly CreateEmetteurHandler $emetteur_handler,
        private readonly CreateInstallationHandler $installation_handler,
        private readonly CreateSystemeHandler $systeme_handler,
    ) {}

    public function __invoke(Payload $payload): Chauffage
    {
        $entity = Chauffage::create();

        foreach ($payload->generateurs as $generateur) {
            $handle = $this->generateur_handler;
            $entity->add_generateur($handle(payload: $generateur, chauffage: $entity));
        }
        foreach ($payload->emetteurs as $emetteur) {
            $handle = $this->emetteur_handler;
            $entity->add_emetteur($handle(payload: $emetteur, chauffage: $entity));
        }
        foreach ($payload->installations as $installation) {
            $handle = $this->installation_handler;
            $entity->add_installation($handle(payload: $installation, chauffage: $entity));
        }
        foreach ($payload->systemes as $systeme) {
            $handle = $this->systeme_handler;
            $entity->add_systeme($handle(payload: $systeme, chauffage: $entity));
        }
        return $entity;
    }
}
