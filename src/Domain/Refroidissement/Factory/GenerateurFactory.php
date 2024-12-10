<?php

namespace App\Domain\Refroidissement\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Refroidissement\Entity\Generateur;
use App\Domain\Refroidissement\Enum\TypeGenerateur;
use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Refroidissement\ValueObject\Signaletique;

final class GenerateurFactory
{
    public function build(
        Id $id,
        Refroidissement $refroidissement,
        string $description,
        Signaletique $signaletique,
        ?int $annee_installation,
        ?Id $reseau_froid_id,
    ): Generateur {
        $entity = new Generateur(
            id: $id,
            refroidissement: $refroidissement,
            description: $description,
            signaletique: $signaletique,
            annee_installation: $annee_installation,
            reseau_froid_id: $signaletique->type_generateur === TypeGenerateur::RESEAU_FROID ? $reseau_froid_id : null,
        );
        $entity->controle();
        return $entity;
    }
}
