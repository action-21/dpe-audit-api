<?php

namespace App\Domain\Enveloppe\ValueObject;

use App\Domain\Common\ValueObject\Annee;
use App\Domain\Enveloppe\Enum\{EtatIsolation, TypeIsolation};
use Webmozart\Assert\Assert;

final class Isolation
{
    public function __construct(
        public readonly ?EtatIsolation $etat_isolation = null,
        public readonly ?TypeIsolation $type_isolation = null,
        public readonly ?Annee $annee_isolation = null,
        public readonly ?float $epaisseur_isolation = null,
        public readonly ?float $resistance_thermique_isolation = null,
    ) {}

    public static function create(
        ?EtatIsolation $etat_isolation,
        ?TypeIsolation $type_isolation = null,
        ?Annee $annee_isolation = null,
        ?float $epaisseur_isolation = null,
        ?float $resistance_thermique_isolation = null,
    ): self {
        Assert::nullOrGreaterThan($epaisseur_isolation, 0);
        Assert::nullOrGreaterThan($resistance_thermique_isolation, 0);

        if ($etat_isolation === null) {
            return new self();
        }
        if ($etat_isolation === EtatIsolation::NON_ISOLE) {
            return new self(etat_isolation: $etat_isolation);
        }
        return new self(
            etat_isolation: EtatIsolation::ISOLE,
            type_isolation: $type_isolation,
            annee_isolation: $annee_isolation,
            epaisseur_isolation: $epaisseur_isolation,
            resistance_thermique_isolation: $resistance_thermique_isolation,
        );
    }
}
