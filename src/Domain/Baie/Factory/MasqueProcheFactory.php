<?php

namespace App\Domain\Baie\Factory;

use App\Domain\Baie\Baie;
use App\Domain\Baie\Entity\MasqueProche;
use App\Domain\Baie\Enum\TypeMasqueProche;
use App\Domain\Common\Type\Id;

final class MasqueProcheFactory
{
    private Baie $baie;
    private string $description;

    public function initialise(Baie $baie, string $description,): self
    {
        $this->baie = $baie;
        $this->description = $description;
        return $this;
    }

    private function build(TypeMasqueProche $type_masque, ?float $avancee = null): MasqueProche
    {
        $entity = new MasqueProche(
            id: Id::create(),
            baie: $this->baie,
            description: $this->description,
            type_masque: $type_masque,
            avancee: $avancee,
        );
        $entity->controle();
        return $entity;
    }

    public function build_balcon_ou_auvent(TypeMasqueProche\BalconAuvent $type, float $avancee,): MasqueProche
    {
        return $this->build(
            type_masque: $type->to(),
            avancee: $avancee,
        );
    }

    public function build_paroi_laterale(TypeMasqueProche\ParoiLaterale $type): MasqueProche
    {
        return $this->build(
            type_masque: $type->to(),
        );
    }
}
