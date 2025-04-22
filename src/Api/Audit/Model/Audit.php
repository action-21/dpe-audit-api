<?php

namespace App\Api\Audit\Model;

use App\Api\Chauffage\Model\Chauffage;
use App\Api\Eclairage\Model\Eclairage;
use App\Api\Ecs\Model\Ecs;
use App\Api\Enveloppe\Model\Enveloppe;
use App\Api\Production\Model\Production;
use App\Api\Refroidissement\Model\Refroidissement;
use App\Api\Ventilation\Model\Ventilation;
use App\Domain\Audit\Audit as Entity;
use Symfony\Component\Validator\Constraints as Assert;

final class Audit
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id,

        #[Assert\Date]
        public readonly string $date_etablissement,

        #[Assert\Valid]
        public readonly Adresse $adresse,

        #[Assert\Valid]
        public readonly Batiment $batiment,

        #[Assert\Valid]
        public readonly Enveloppe $enveloppe,

        #[Assert\Valid]
        public readonly Chauffage $chauffage,

        #[Assert\Valid]
        public readonly Ecs $ecs,

        #[Assert\Valid]
        public readonly Ventilation $ventilation,

        #[Assert\Valid]
        public readonly Refroidissement $refroidissement,

        #[Assert\Valid]
        public readonly Production $production,

        public readonly ?Eclairage $eclairage,

        public readonly ?AuditData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            date_etablissement: $entity->date_etablissement()->format('Y-m-d'),
            adresse: Adresse::from($entity),
            batiment: Batiment::from($entity),
            enveloppe: Enveloppe::from($entity->enveloppe()),
            chauffage: Chauffage::from($entity->chauffage()),
            ecs: Ecs::from($entity->ecs()),
            ventilation: Ventilation::from($entity->ventilation()),
            refroidissement: Refroidissement::from($entity->refroidissement()),
            production: Production::from($entity->production()),
            eclairage: Eclairage::from($entity->eclairage()),
            data: AuditData::from($entity),
        );
    }
}
