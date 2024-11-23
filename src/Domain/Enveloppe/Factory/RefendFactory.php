<?php

namespace App\Domain\Enveloppe\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Entity\Refend;
use App\Domain\Enveloppe\Enum\Inertie\InertieRefend;
use App\Domain\Enveloppe\Enveloppe;

final class RefendFactory
{
    public function build(
        Enveloppe $enveloppe,
        string $description,
        float $surface,
        InertieRefend $inertie,
    ): Refend {
        $entity = new Refend(
            id: Id::create(),
            enveloppe: $enveloppe,
            description: $description,
            surface: $surface,
            inertie: $inertie->to(),
        );
        $entity->controle();
        return $entity;
    }
}
