<?php

namespace App\Domain\Mur\ValueObject;

use App\Domain\Common\Service\Assert;
use App\Domain\Mur\Enum\{EtatIsolation, TypeIsolation};
use App\Domain\Mur\Mur;

final class Isolation
{
    public function __construct(
        public readonly EtatIsolation $etat_isolation,
        public readonly ?TypeIsolation $type_isolation = null,
        public readonly ?int $annee_isolation = null,
        public readonly ?int $epaisseur_isolation = null,
        public readonly ?float $resistance_thermique_isolation = null,
    ) {}

    public function applique_travaux(Travaux $travaux): self
    {
        return new self(
            etat_isolation: EtatIsolation::ISOLE,
            type_isolation: $travaux->type_isolation->to(),
            annee_isolation: (new \DateTime())->format('Y'),
            epaisseur_isolation: $travaux->epaisseur_isolation,
            resistance_thermique_isolation: $travaux->resistance_thermique_isolation,
        );
    }

    public static function create_inconnu(): self
    {
        return new self(etat_isolation: EtatIsolation::INCONNU);
    }

    public static function create_non_isole(): self
    {
        return new self(etat_isolation: EtatIsolation::NON_ISOLE);
    }

    public static function create_isole(
        TypeIsolation $type_isolation,
        ?int $annee_isolation,
        ?int $epaisseur_isolation,
        ?float $resistance_thermique_isolation,
    ): self {
        return new self(
            etat_isolation: EtatIsolation::ISOLE,
            type_isolation: $type_isolation,
            annee_isolation: $annee_isolation,
            epaisseur_isolation: $epaisseur_isolation,
            resistance_thermique_isolation: $resistance_thermique_isolation,
        );
    }

    public function controle(Mur $entity): void
    {
        Assert::annee($this->annee_isolation);
        Assert::positif($this->epaisseur_isolation);
        Assert::positif($this->resistance_thermique_isolation);
        Assert::superieur_ou_egal_a($this->annee_isolation, $entity->annee_construction_defaut());
    }

    public function etat_isolation_defaut(int $annee_construction): EtatIsolation
    {
        if ($this->etat_isolation === EtatIsolation::INCONNU)
            return $annee_construction < 1975 ? EtatIsolation::NON_ISOLE : EtatIsolation::ISOLE;

        return $this->etat_isolation;
    }

    public function annee_isolation_defaut(int $annee_construction): ?int
    {
        if ($this->annee_isolation)
            return $this->annee_isolation;

        if ($this->etat_isolation !== EtatIsolation::ISOLE)
            return null;

        return $annee_construction <= 1974 ? 1976 : $annee_construction;
    }
}
