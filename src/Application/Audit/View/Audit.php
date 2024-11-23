<?php

namespace App\Application\Audit\View;

use App\Domain\Audit\Audit as Entity;
use App\Domain\Audit\ValueObject\{Adresse, Batiment, Logement, Occupation};
use App\Domain\Common\Type\Id;

final class Audit
{
    public function __construct(
        public readonly Id $id,
        public readonly string $date_creation,
        public readonly Adresse $adresse,
        public readonly Batiment $batiment,
        public readonly ?Logement $logement,
        public readonly ?Occupation $occupation,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            date_creation: $entity->date_creation()->format('Y-m-d'),
            adresse: $entity->adresse(),
            batiment: $entity->batiment(),
            logement: $entity->logement(),
            occupation: $entity->occupation(),
        );
    }
}
