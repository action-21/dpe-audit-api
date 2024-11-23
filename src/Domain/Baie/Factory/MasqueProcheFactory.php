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

    public function build_fond_balcon_ou_fond_flanc_loggias(float $avancee,): MasqueProche
    {
        return $this->build(
            type_masque: TypeMasqueProche::FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS,
            avancee: $avancee,
        );
    }

    public function build_balcon_ou_auvent(float $avancee,): MasqueProche
    {
        return $this->build(
            type_masque: TypeMasqueProche::BALCON_OU_AUVENT,
            avancee: $avancee,
        );
    }

    public function build_paroi_laterale(bool $retour_sud,): MasqueProche
    {
        return $this->build(
            type_masque: $retour_sud
                ? TypeMasqueProche::PAROI_LATERALE_AVEC_OBSTACLE_AU_SUD
                : TypeMasqueProche::PAROI_LATERALE_SANS_OBSTACLE_AU_SUD,
        );
    }
}
