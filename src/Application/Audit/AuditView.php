<?php

namespace App\Application\Audit;

use App\Application\Batiment\BatimentView;
use App\Domain\Audit\{Audit, AuditEngine};
use App\Domain\Common\Enum\Enum;

class AuditView
{
    public function __construct(
        public readonly string $id,
        public readonly \DateTimeImmutable $date_creation,
        public readonly Enum $methode_calcul,
        public readonly Enum $perimetre_application,
        public  readonly ?BatimentView $batiment,
    ) {
    }

    public static function from_entity(Audit $entity): self
    {
        return new self(
            id: $entity->id(),
            date_creation: $entity->date_creation(),
            methode_calcul: $entity->methode_calcul(),
            perimetre_application: $entity->perimetre_application(),
            batiment: $entity->batiment() ? BatimentView::from_entity($entity->batiment()) : null,
        );
    }

    public static function from_engine(AuditEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            id: $entity->id(),
            date_creation: $entity->date_creation(),
            methode_calcul: $entity->methode_calcul(),
            perimetre_application: $entity->perimetre_application(),
            batiment: $engine->batiment_engine() ? BatimentView::from_engine($engine->batiment_engine()) : null,
        );
    }
}
