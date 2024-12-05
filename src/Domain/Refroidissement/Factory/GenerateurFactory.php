<?php

namespace App\Domain\Refroidissement\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Refroidissement\Entity\Generateur;
use App\Domain\Refroidissement\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Refroidissement\Refroidissement;

final class GenerateurFactory
{
    private Id $id;
    private Refroidissement $refroidissement;
    private string $description;
    private ?int $annee_installation;

    public function initialise(
        Id $id,
        Refroidissement $refroidissement,
        string $description,
        ?int $annee_installation,
    ): self {
        $this->id = $id;
        $this->refroidissement = $refroidissement;
        $this->description = $description;
        $this->annee_installation = $annee_installation;
        return $this;
    }

    private function build(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ?float $seer = null,
        ?Id $reseau_froid_id = null,
    ): Generateur {
        $entity = new Generateur(
            id: $this->id,
            refroidissement: $this->refroidissement,
            description: $this->description,
            type_generateur: $type_generateur,
            energie_generateur: $energie_generateur,
            annee_installation: $this->annee_installation,
            seer: $seer,
            reseau_froid_id: $reseau_froid_id,
        );
        $entity->controle();
        return $entity;
    }

    public function build_climatiseur(
        TypeGenerateur\Climatiseur $type_generateur,
        EnergieGenerateur $energie,
        ?float $seer,
    ): Generateur {
        return $this->build(
            type_generateur: ($type_generateur = $type_generateur->to()),
            energie_generateur: $type_generateur->is_thermodynamique() ? EnergieGenerateur::ELECTRICITE : $energie,
            seer: $seer,
        );
    }

    public function build_reseau_froid(?Id $reseau_froid_id): Generateur
    {
        return $this->build(
            type_generateur: TypeGenerateur::RESEAU_FROID,
            energie_generateur: EnergieGenerateur::RESEAU_FROID,
            reseau_froid_id: $reseau_froid_id,
        );
    }
}
