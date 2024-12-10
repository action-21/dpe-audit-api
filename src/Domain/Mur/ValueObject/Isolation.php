<?php

namespace App\Domain\Mur\ValueObject;

use App\Domain\Mur\Enum\{EtatIsolation, TypeIsolation};
use Webmozart\Assert\Assert;

final class Isolation
{
    public function __construct(
        public readonly EtatIsolation $etat_isolation,
        public readonly ?TypeIsolation $type_isolation = null,
        public readonly ?int $annee_isolation = null,
        public readonly ?int $epaisseur_isolation = null,
        public readonly ?float $resistance_thermique_isolation = null,
    ) {}

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
        Assert::nullOrLessThanEq($annee_isolation, (int) \date('Y'));
        Assert::nullOrGreaterThan($epaisseur_isolation, 0);
        Assert::nullOrGreaterThan($resistance_thermique_isolation, 0);

        return new self(
            etat_isolation: EtatIsolation::ISOLE,
            type_isolation: $type_isolation,
            annee_isolation: $annee_isolation,
            epaisseur_isolation: $epaisseur_isolation,
            resistance_thermique_isolation: $resistance_thermique_isolation,
        );
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
