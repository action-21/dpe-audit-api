<?php

namespace App\Api\Ventilation;

use App\Api\Ventilation\Payload\{GenerateurPayload, InstallationPayload, SystemePayload, VentilationPayload};
use App\Domain\Audit\Audit;
use App\Domain\Ventilation\{Ventilation, VentilationFactory};
use App\Domain\Ventilation\Entity\Installation;
use App\Domain\Ventilation\Factory\{GenerateurFactory, InstallationFactory, SystemeFactory};

final class CreateVentilationHandler
{
    public function __construct(
        private VentilationFactory $ventilation_factory,
        private GenerateurFactory $generateur_factory,
        private InstallationFactory $installation_factory,
        private SystemeFactory $systeme_factory,
    ) {}

    public function __invoke(VentilationPayload $payload, Audit $audit,): Ventilation
    {
        $ventilation = $this->ventilation_factory->build(audit: $audit);

        foreach ($payload->generateurs as $generateur) {
            $this->set_generateur($generateur, $ventilation);
        }
        foreach ($payload->installations as $installation) {
            $this->set_installation($installation, $ventilation);
        }
        return $ventilation;
    }

    private function set_generateur(GenerateurPayload $payload, Ventilation $ventilation): void
    {
        $ventilation->add_generateur($this->generateur_factory->build(
            id: $payload->id(),
            ventilation: $ventilation,
            description: $payload->description,
            signaletique: $payload->signaletique->to(),
            generateur_collectif: $payload->generateur_collectif,
            annee_installation: $payload->annee_installation,
        ));
    }

    private function set_installation(InstallationPayload $payload, Ventilation $ventilation): void
    {
        $installation = $this->installation_factory->build(
            id: $payload->id(),
            ventilation: $ventilation,
            surface: $payload->surface,
        );

        foreach ($payload->systemes as $systeme) {
            $this->set_systeme($systeme, $installation);
        }
        $ventilation->add_installation($installation);
    }

    private function set_systeme(SystemePayload $payload, Installation $installation): void
    {
        $factory = ($this->systeme_factory)(id: $payload->id(), installation: $installation);

        if ($payload->type_ventilation_naturelle()) {
            $systeme = $factory->build_ventilation_naturelle(type_ventilation: $payload->type_ventilation_naturelle());
            $installation->add_systeme($systeme);
            return;
        }
        if (null === $generateur = $installation->ventilation()->generateurs()->find($payload->generateur_id())) {
            throw new \InvalidArgumentException('Generateur not found');
        }
        $systeme = $factory->build_ventilation_mecanique(generateur: $generateur);
        $installation->add_systeme($systeme);
    }
}
