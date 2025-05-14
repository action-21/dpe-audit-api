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

/**
 * @property Logement[] $logements
 */
final class Audit
{
    public function __construct(
        public string $id,

        #[Assert\Date]
        public string $date_etablissement,

        #[Assert\Valid]
        public Adresse $adresse,

        #[Assert\Valid]
        public Batiment $batiment,

        /** @var Logement[] */
        #[Assert\All([new Assert\Type(Logement::class)])]
        #[Assert\Valid]
        public array $logements,

        #[Assert\Valid]
        public Enveloppe $enveloppe,

        #[Assert\Valid]
        public Chauffage $chauffage,

        #[Assert\Valid]
        public Ecs $ecs,

        #[Assert\Valid]
        public Ventilation $ventilation,

        #[Assert\Valid]
        public Refroidissement $refroidissement,

        #[Assert\Valid]
        public Production $production,

        public ?Eclairage $eclairage,

        public ?AuditData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            date_etablissement: $entity->date_etablissement()->format('Y-m-d'),
            adresse: Adresse::from($entity),
            batiment: Batiment::from($entity),
            logements: Logement::from_collection($entity->logements()),
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
