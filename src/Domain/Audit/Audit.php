<?php

namespace App\Domain\Audit;

use App\Domain\Audit\Entity\{Logement, LogementCollection};
use App\Domain\Audit\ValueObject\{Adresse, Batiment};
use App\Domain\Chauffage\Chauffage;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Eclairage\Eclairage;
use App\Domain\Ecs\Ecs;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Production\Production;
use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Ventilation\Ventilation;

final class Audit
{
    public function __construct(
        protected readonly Id $id,
        private \DateTimeImmutable $date_etablissement,
        private Adresse $adresse,
        private Batiment $batiment,
        private LogementCollection $logements,
        private Enveloppe $enveloppe,
        private Ventilation $ventilation,
        private Chauffage $chauffage,
        private Ecs $ecs,
        private Refroidissement $refroidissement,
        private Eclairage $eclairage,
        private Production $production,
        private AuditData $data,
    ) {}

    public static function create(
        Adresse $adresse,
        Batiment $batiment,
        Enveloppe $enveloppe,
        Ventilation $ventilation,
        Chauffage $chauffage,
        Ecs $ecs,
        Refroidissement $refroidissement,
        Production $production,
    ): self {
        return new self(
            id: Id::create(),
            date_etablissement: new \DateTimeImmutable(),
            adresse: $adresse,
            batiment: $batiment,
            logements: new LogementCollection,
            enveloppe: $enveloppe,
            ventilation: $ventilation,
            chauffage: $chauffage,
            ecs: $ecs,
            refroidissement: $refroidissement,
            eclairage: Eclairage::create(),
            production: $production,
            data: AuditData::create(),
        );
    }

    public function reinitialise(): void
    {
        $this->data = AuditData::create();
        $this->enveloppe->reinitialise();
        $this->ventilation->reinitialise();
        $this->chauffage->reinitialise();
        $this->ecs->reinitialise();
        $this->refroidissement->reinitialise();
        $this->eclairage->reinitialise();
        $this->production->reinitialise();
    }

    public function calcule(AuditData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function date_etablissement(): \DateTimeImmutable
    {
        return $this->date_etablissement;
    }

    public function adresse(): Adresse
    {
        return $this->adresse;
    }

    public function batiment(): Batiment
    {
        return $this->batiment;
    }

    /**
     * @return LogementCollection|Logement[]
     */
    public function logements(): LogementCollection
    {
        return $this->logements;
    }

    public function add_logement(Logement $entity): self
    {
        $this->logements->add($entity);
        return $this;
    }

    public function enveloppe(): Enveloppe
    {
        return $this->enveloppe;
    }

    public function ventilation(): Ventilation
    {
        return $this->ventilation;
    }

    public function chauffage(): Chauffage
    {
        return $this->chauffage;
    }

    public function ecs(): Ecs
    {
        return $this->ecs;
    }

    public function refroidissement(): Refroidissement
    {
        return $this->refroidissement;
    }

    public function eclairage(): Eclairage
    {
        return $this->eclairage;
    }

    public function production(): Production
    {
        return $this->production;
    }

    public function data(): AuditData
    {
        return $this->data;
    }
}
