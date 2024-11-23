<?php

namespace App\Domain\Chauffage\Factory;

use App\Domain\Chauffage\Entity\{EmetteurCollection, Generateur, Installation, Systeme};
use App\Domain\Chauffage\Enum\{CategorieGenerateur, TypeDistribution};
use App\Domain\Chauffage\ValueObject\Reseau;
use App\Domain\Common\Type\Id;

final class SystemeFactory
{
    private Id $id;
    private Installation $installation;
    private Generateur $generateur;

    public function initialise(
        Id $id,
        Installation $installation,
        Generateur $generateur,
    ): self {
        $this->id = $id;
        $this->installation = $installation;
        $this->generateur = $generateur;
        return $this;
    }

    public function build_systeme_divise(): Systeme
    {
        if (false === $this->generateur->type()->chauffage_divise())
            throw new \InvalidArgumentException('Générateur incompatible');

        $entity = new Systeme(
            id: $this->id,
            installation: $this->installation,
            generateur: $this->generateur,
            position_volume_chauffe: true,
            reseau: null,
            type_distribution: TypeDistribution::SANS,
            emetteurs: new EmetteurCollection(),
        );
        $entity->controle();
        return $entity;
    }

    public function build_systeme_central(
        TypeDistribution $type_distribution,
        Reseau $reseau,
        bool $position_volume_chauffe,
    ): Systeme {
        if (false === $this->generateur->type()->chauffage_central())
            throw new \InvalidArgumentException('Générateur incompatible');

        if (\in_array($this->generateur->categorie(), [
            CategorieGenerateur::CHAUDIERE_MULTI_BATIMENT,
            CategorieGenerateur::PAC_MULTI_BATIMENT,
            CategorieGenerateur::RESEAU_CHALEUR,
        ])) $position_volume_chauffe = false;

        $entity = new Systeme(
            id: $this->id,
            installation: $this->installation,
            generateur: $this->generateur,
            type_distribution: $type_distribution,
            reseau: $reseau,
            position_volume_chauffe: $position_volume_chauffe,
            emetteurs: new EmetteurCollection(),
        );
        $entity->controle();
        return $entity;
    }
}
