<?php

namespace App\Domain\Ventilation\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\Entity\Generateur;
use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVentilation};
use App\Domain\Ventilation\Ventilation;

final class GenerateurFactory
{
    private Id $id;
    private Ventilation $ventilation;
    private string $description;
    private ?int $annee_installation;

    public function initialise(Id $id, Ventilation $ventilation, string $description, ?int $annee_installation): self
    {
        $this->id = $id;
        $this->ventilation = $ventilation;
        $this->description = $description;
        $this->annee_installation = $annee_installation;
        return $this;
    }

    public function build_generateur_centralise(
        TypeGenerateur\TypeGenerateurCentralise $type,
        bool $presence_echangeur_thermique,
        bool $generateur_collectif,
    ): Generateur {
        return new Generateur(
            id: $this->id,
            ventilation: $this->ventilation,
            description: $this->description,
            type_ventilation: TypeVentilation::VENTILATION_MECANIQUE_CENTRALISEE,
            type: $type->to(),
            annee_installation: $this->annee_installation,
            presence_echangeur_thermique: $presence_echangeur_thermique,
            generateur_collectif: $generateur_collectif,
        );
    }

    public function build_generateur_divise(
        TypeGenerateur\TypeGenerateurDivise $type,
    ): Generateur {
        return new Generateur(
            id: $this->id,
            ventilation: $this->ventilation,
            description: $this->description,
            type_ventilation: TypeVentilation::VENTILATION_MECANIQUE_CENTRALISEE,
            type: $type->to(),
            annee_installation: $this->annee_installation,
            presence_echangeur_thermique: false,
            generateur_collectif: false,
        );
    }
}
