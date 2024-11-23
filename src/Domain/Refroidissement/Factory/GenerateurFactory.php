<?php

namespace App\Domain\Refroidissement\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Refroidissement\Entity\Generateur;
use App\Domain\Refroidissement\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Refroidissement\Enum\TypeGenerateur\{TypeAutres, TypeThermodynamique};
use App\Domain\Refroidissement\Refroidissement;

final class GenerateurFactory
{
    private Id $id;
    private Refroidissement $refroidissement;
    private string $description;
    private ?int $annee_installation;
    private ?float $seer;

    public function initialise(
        Id $id,
        Refroidissement $refroidissement,
        string $description,
        ?int $annee_installation,
        ?float $seer
    ): self {
        $this->id = $id;
        $this->refroidissement = $refroidissement;
        $this->description = $description;
        $this->annee_installation = $annee_installation;
        $this->seer = $seer;
        return $this;
    }

    private function build(TypeGenerateur $type_generateur, EnergieGenerateur $energie_generateur): Generateur
    {
        $entity = new Generateur(
            id: $this->id,
            refroidissement: $this->refroidissement,
            description: $this->description,
            type_generateur: $type_generateur,
            energie_generateur: $energie_generateur,
            annee_installation: $this->annee_installation,
            seer: $this->seer,
        );
        $entity->controle();
        return $entity;
    }

    public function build_systeme_thermodynamique(TypeThermodynamique $type_generateur): Generateur
    {
        return $this->build(
            type_generateur: $type_generateur->to(),
            energie_generateur: EnergieGenerateur::ELECTRICITE,
        );
    }

    public function build_reseau_froid(): Generateur
    {
        return $this->build(
            type_generateur: TypeGenerateur::RESEAU_FROID,
            energie_generateur: EnergieGenerateur::RESEAU_FROID,
        );
    }

    public function build_autres_systemes(TypeAutres $type_generateur, EnergieGenerateur $energie,): Generateur
    {
        return $this->build(
            type_generateur: $type_generateur->to(),
            energie_generateur: $energie,
        );
    }
}
