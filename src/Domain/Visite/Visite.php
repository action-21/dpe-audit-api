<?php

namespace App\Domain\Visite;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Visite\Entity\{Logement, LogementCollection};

final class Visite
{
    public function __construct(
        private readonly Audit $audit,
        private LogementCollection $logements,
    ) {}

    public static function create(Audit $audit): self
    {
        return new self(
            audit: $audit,
            logements: new LogementCollection(),
        );
    }

    public function reinitialise(): void {}

    /**
     * TODO: Implémenter des modes de contrôle stricte et permissif
     */
    public function controle(): void
    {
        if ($this->audit->batiment()->type !== TypeBatiment::IMMEUBLE)
            return;

        $taux_logements_visites = $this->logements->count() / $this->audit->batiment()->logements * 100;

        if ($this->audit->batiment()->logements > 30 && $this->audit->batiment()->logements <= 100 && $taux_logements_visites < 10)
            throw new \DomainException('Le taux de logements visités doit être supérieur à 10%');

        if ($this->audit->batiment()->logements > 100 && $taux_logements_visites < 5)
            throw new \DomainException('Le taux de logements visités doit être supérieur à 5%');
    }

    public function audit(): Audit
    {
        return $this->audit;
    }

    public function logements(): LogementCollection
    {
        return $this->logements;
    }

    public function add_logement(Logement $entity): self
    {
        $this->logements->add($entity);
        return $this;
    }

    public function remove_logement(Logement $entity): self
    {
        $this->logements->remove($entity);
        return $this;
    }
}
