<?php

namespace App\Domain\Audit;

use App\Domain\Audit\ValueObject\{Adresse, Batiment, Logement};
use App\Domain\Common\Type\Id;

final class AuditFactory
{
    public function build(Adresse $adresse, Batiment $batiment, ?Logement $logement, ?Id $id = null): Audit
    {
        $entity = new Audit(
            id: $id ?? Id::create(),
            date_creation: new \DateTimeImmutable(),
            adresse: $adresse,
            batiment: $batiment,
            logement: $logement,
        );
        $entity->controle();
        return $entity;
    }
}
