<?php

namespace App\Domain\Baie\Factory;

use App\Domain\Baie\Baie;
use App\Domain\Baie\Entity\MasqueLointain;
use App\Domain\Baie\Enum\TypeMasqueLointain;
use App\Domain\Common\Type\Id;

final class MasqueLointainFactory
{
    public function build(
        Baie $baie,
        string $description,
        TypeMasqueLointain $type_masque,
        float $hauteur,
        float $orientation,
    ): MasqueLointain {
        $entity = new MasqueLointain(
            id: Id::create(),
            baie: $baie,
            description: $description,
            type_masque: $type_masque,
            hauteur: $hauteur,
            orientation: $orientation,
        );
        $entity->controle();
        return $entity;
    }
}
