<?php

namespace App\Domain\Audit;

use App\Domain\Audit\Enum\{MethodeCalcul, PerimetreApplication};
use App\Domain\Audit\ValueObject\Auditeur;
use App\Domain\Batiment\Batiment;
use App\Domain\Common\ValueObject\Id;

final class Audit
{
    public function __construct(
        private readonly Id $id,
        private readonly \DateTimeImmutable $date_creation,
        private readonly MethodeCalcul $methode_calcul,
        private readonly PerimetreApplication $perimetre_application,
        private Auditeur $auditeur,
        private ?Batiment $batiment = null,
    ) {
    }

    public static function create(
        MethodeCalcul $methode_calcul,
        PerimetreApplication $perimetre_application,
        Auditeur $auditeur,
    ): self {
        return new self(
            id: Id::create(),
            date_creation: new \DateTimeImmutable(),
            methode_calcul: $methode_calcul,
            perimetre_application: $perimetre_application,
            auditeur: $auditeur,
        );
    }

    public function update(Auditeur $auditeur): self
    {
        $this->auditeur = $auditeur;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function date_creation(): \DateTimeImmutable
    {
        return $this->date_creation;
    }

    public function methode_calcul(): MethodeCalcul
    {
        return $this->methode_calcul;
    }

    public function perimetre_application(): PerimetreApplication
    {
        return $this->perimetre_application;
    }

    public function auditeur(): Auditeur
    {
        return $this->auditeur;
    }

    public function batiment(): ?Batiment
    {
        return $this->batiment;
    }

    public function set_batiment(Batiment $entity): self
    {
        $this->batiment = $entity;
        return $this;
    }
}
