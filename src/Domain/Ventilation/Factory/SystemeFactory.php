<?php

namespace App\Domain\Ventilation\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\Entity\{Generateur, Installation, Systeme};
use App\Domain\Ventilation\Enum\TypeVentilation;

final class SystemeFactory
{
    private Id $id;
    private Installation $installation;

    public function initialise(Id $id, Installation $installation): self
    {
        $this->id = $id;
        $this->installation = $installation;
        return $this;
    }

    private function build(TypeVentilation $type_ventilation, ?Generateur $generateur = null,): Systeme
    {
        $entity = new Systeme(
            id: $this->id,
            installation: $this->installation,
            type_ventilation: $type_ventilation,
            generateur: $generateur,
        );

        $entity->controle();
        return $entity;
    }

    public function build_ventilation_naturelle(TypeVentilation\VentilationNaturelle $type_ventilation,): Systeme
    {
        return $this->build(
            type_ventilation: $type_ventilation->to(),
        );
    }

    public function build_ventilation_mecanique(Generateur $generateur,): Systeme
    {
        return $this->build(
            type_ventilation: TypeVentilation::VENTILATION_MECANIQUE,
            generateur: $generateur,
        );
    }
}
