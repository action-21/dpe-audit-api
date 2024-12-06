<?php

namespace App\Domain\Ventilation\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\Entity\Generateur;
use App\Domain\Ventilation\ValueObject\Signaletique;
use App\Domain\Ventilation\Ventilation;

final class GenerateurFactory
{
    public function build(Id $id, Ventilation $ventilation, string $description, ?Signaletique $signaletique): Generateur
    {
        $entity = new Generateur(
            id: $id,
            ventilation: $ventilation,
            description: $description,
            signaletique: $signaletique,
        );
        $entity->controle();
        return $entity;
    }
}
